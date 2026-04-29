<?php

namespace App\Services\Arca;

use App\Services\Arca\WsaaClient;
use Exception;
use Illuminate\Support\Facades\Log;

class WsfeClient
{
    protected string $endpoint;
    protected string $cuit;
    protected int $puntoVenta;
    protected WsaaClient $wsaa;
    protected ?\SoapClient $soapClient = null;
    protected array $informationalEvents = [];

    public function __construct()
    {
        $config = config('arca');
        $env = $config['environment'] ?? 'homologation';

        $this->endpoint = $config['wsfe'][$env];
        $this->cuit = $config['cuit'];
        $this->puntoVenta = (int) $config['punto_venta'];
        $this->wsaa = new WsaaClient('wsfe');
    }

    /**
     * Obtiene el SoapClient autenticado con token y sign.
     */
    protected function getClient(): \SoapClient
    {
        if ($this->soapClient) {
            return $this->soapClient;
        }

        $ta = $this->wsaa->getTicketAcceso();

        // AFIP's WSFE server uses a legacy DH key rejected by OpenSSL 3.x at SECLEVEL=2.
        $sslContext = stream_context_create(['ssl' => ['ciphers' => 'DEFAULT@SECLEVEL=1']]);

        $this->soapClient = new \SoapClient($this->endpoint, [
            'soap_version' => SOAP_1_2,
            'trace' => true,
            'exceptions' => true,
            'stream_context' => $sslContext,
        ]);

        return $this->soapClient;
    }

    /**
     * Construye las credenciales de autenticación para las llamadas WSFEv1.
     */
    protected function authArray(): array
    {
        $ta = $this->wsaa->getTicketAcceso();

        return [
            'Token' => $ta['token'],
            'Sign' => $ta['sign'],
            'Cuit' => (int) $this->cuit,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Métodos de consulta (WSFEv1)
    |--------------------------------------------------------------------------
    */

    /**
     * Obtiene el último comprobante autorizado.
     */
    public function getLastComprobante(int $tipoCbte): int
    {
        $client = $this->getClient();

        $result = $client->FECompUltimoAutorizado([
            'Auth' => $this->authArray(),
            'PtoVta' => $this->puntoVenta,
            'CbteTipo' => $tipoCbte,
        ]);

        $this->checkErrors($result);

        return (int) $result->FECompUltimoAutorizadoResult->CbteNro;
    }

    /**
     * Obtiene tipos de comprobante disponibles.
     */
    public function getTiposComprobante(): array
    {
        $client = $this->getClient();

        $result = $client->FEParamGetTiposCbte([
            'Auth' => $this->authArray(),
        ]);

        $this->checkErrors($result);

        $tipos = [];
        if (isset($result->FEParamGetTiposCbteResult->ResultGet->CbteTipo)) {
            foreach ($this->soapItems($result->FEParamGetTiposCbteResult->ResultGet->CbteTipo) as $tipo) {
                $id = (int) $this->soapField($tipo, 'Id');
                $tipos[$id] = [
                    'id' => $id,
                    'descripcion' => (string) $this->soapField($tipo, 'Desc'),
                    'fecha_vigencia_desde' => (string) ($this->soapField($tipo, 'FchDesde') ?? ''),
                    'fecha_vigencia_hasta' => (string) ($this->soapField($tipo, 'FchHasta') ?? ''),
                ];
            }
        }

        return $tipos;
    }

    /**
     * Obtiene tipos de documento disponibles.
     */
    public function getTiposDocumento(): array
    {
        $client = $this->getClient();

        $result = $client->FEParamGetTiposDoc([
            'Auth' => $this->authArray(),
        ]);

        $this->checkErrors($result);

        $tipos = [];
        if (isset($result->FEParamGetTiposDocResult->ResultGet->DocTipo)) {
            foreach ($this->soapItems($result->FEParamGetTiposDocResult->ResultGet->DocTipo) as $tipo) {
                $tipos[(int) $this->soapField($tipo, 'Id')] = (string) $this->soapField($tipo, 'Desc');
            }
        }

        return $tipos;
    }

    /**
     * Obtiene tipos de alícuota de IVA.
     */
    public function getTiposIva(): array
    {
        $client = $this->getClient();

        $result = $client->FEParamGetTiposIva([
            'Auth' => $this->authArray(),
        ]);

        $this->checkErrors($result);

        $tipos = [];
        if (isset($result->FEParamGetTiposIvaResult->ResultGet->IvaTipo)) {
            foreach ($this->soapItems($result->FEParamGetTiposIvaResult->ResultGet->IvaTipo) as $tipo) {
                $tipos[(int) $this->soapField($tipo, 'Id')] = (string) $this->soapField($tipo, 'Desc');
            }
        }

        return $tipos;
    }

    /**
     * Obtiene tipos de moneda.
     */
    public function getTiposMoneda(): array
    {
        $client = $this->getClient();

        $result = $client->FEParamGetTiposMonedas([
            'Auth' => $this->authArray(),
        ]);

        $this->checkErrors($result);

        $tipos = [];
        if (isset($result->FEParamGetTiposMonedasResult->ResultGet->Moneda)) {
            foreach ($this->soapItems($result->FEParamGetTiposMonedasResult->ResultGet->Moneda) as $tipo) {
                $tipos[(string) $this->soapField($tipo, 'Id')] = (string) $this->soapField($tipo, 'Desc');
            }
        }

        return $tipos;
    }

    /**
     * Obtiene la cotización de una moneda.
     */
    public function getCotizacionMoneda(string $moneda): float
    {
        $client = $this->getClient();

        $result = $client->FEParamGetCotizacion([
            'Auth' => $this->authArray(),
            'MonId' => $moneda,
        ]);

        $this->checkErrors($result);

        return (float) $result->FEParamGetCotizacionResult->MonCotizacion;
    }

    /**
     * Consulta el estado de los servidores de ARCA/AFIP.
     */
    public function getServerStatus(): array
    {
        $client = $this->getClient();

        $result = $client->FEDummy();

        return [
            'appserver' => (string) ($result->FEDummyResult->AppServer ?? 'N/A'),
            'dbserver' => (string) ($result->FEDummyResult->DbServer ?? 'N/A'),
            'authserver' => (string) ($result->FEDummyResult->AuthServer ?? 'N/A'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Métodos de emisión (WSFEv1)
    |--------------------------------------------------------------------------
    */

    /**
     * Autoriza un comprobante electrónico (Factura C).
     *
     * @param array $data Datos del comprobante:
     *   - concepto: int (1=productos, 2=servicios, 3=ambos)
     *   - doc_tipo: int (96=DNI, 80=CUIT)
     *   - doc_nro: string (número de documento)
     *   - cbte_desde: int (número de comprobante)
     *   - cbte_hasta: int (número de comprobante)
     *   - fecha: string (YYYYMMDD)
     *   - imp_total: float
     *   - imp_tot_conc: float (no gravado)
     *   - imp_neto: float (gravado)
     *   - imp_iva: float
     *   - imp_op_ex: float (exento)
     *   - imp_perc: float (percepciones)
     *   - imp_trib: float (otros tributos)
     *   - moneda: string (PES)
     *   - moneda_ctz: float (1 para pesos)
     *   - iva: array [['id' => 5, 'base' => 1000, 'importe' => 210]]
     */
    public function autorizarComprobante(array $data): array
    {
        if (!config('arca.enable_issuing')) {
            throw new \RuntimeException('ARCA issuing is disabled. Set ARCA_ENABLE_ISSUING=true to enable.');
        }

        $config = config('arca');

        $cbteDesde = $data['cbte_desde'] ?? ($this->getLastComprobante($config['tipo_comprobante']) + 1);
        $cbteHasta = $data['cbte_hasta'] ?? $cbteDesde;

        $req = [
            'Auth' => $this->authArray(),
            'FeCAEReq' => [
                'FeCabReq' => [
                    'CantReg' => 1,
                    'PtoVta' => $this->puntoVenta,
                    'CbteTipo' => $config['tipo_comprobante'],
                ],
                'FeDetReq' => [
                    'FECAEDetRequest' => [
                        'Concepto' => $data['concepto'] ?? $config['concepto'],
                        'DocTipo' => $data['doc_tipo'] ?? $config['tipo_documento'],
                        'DocNro' => $data['doc_nro'] ?? '0',
                        'CbteDesde' => $cbteDesde,
                        'CbteHasta' => $cbteHasta,
                        'CbteFch' => $data['fecha'] ?? date('Ymd'),
                        'ImpTotal' => round($data['imp_total'] ?? 0, 2),
                        'ImpTotConc' => round($data['imp_tot_conc'] ?? 0, 2),
                        'ImpNeto' => round($data['imp_neto'] ?? 0, 2),
                        'ImpOpEx' => round($data['imp_op_ex'] ?? 0, 2),
                        'ImpIVA' => round($data['imp_iva'] ?? 0, 2),
                        'ImpTrib' => round($data['imp_trib'] ?? 0, 2),
                        'ImpAutop' => round($data['imp_autop'] ?? 0, 2),
                        'MonId' => $data['moneda'] ?? $config['moneda'],
                        'MonCotiz' => $data['moneda_ctz'] ?? $config['moneda_ctz'],
                    ],
                ],
            ],
        ];

        // Agregar IVA si existe
        if (!empty($data['iva'])) {
            $req['FeCAEReq']['FeDetReq']['FECAEDetRequest']['Iva'] = [];
            foreach ($data['iva'] as $iva) {
                $req['FeCAEReq']['FeDetReq']['FECAEDetRequest']['Iva'][] = [
                    'Id' => $iva['id'],
                    'BaseImp' => round($iva['base'], 2),
                    'Importe' => round($iva['importe'], 2),
                ];
            }
        }

        $client = $this->getClient();

        $result = $client->FECAESolicitar($req);

        $this->checkErrors($result);

        $caeDet = $result->FECAESolicitarResult->FeCabResp;
        $detalle = $result->FECAESolicitarResult->FeDetReq->FECAEDetResponse ?? $result->FECAESolicitarResult->FeDetResp->FECAEDetResponse;

        return [
            'cae' => (string) $detalle->CAE,
            'cae_vencimiento' => (string) $detalle->CAEFchVto,
            'resultado' => (string) $detalle->Resultado,
            'cbte_tipo' => (int) $detalle->CbteTipo,
            'cbte_desde' => (int) $detalle->CbteDesde,
            'cbte_hasta' => (int) $detalle->CbteHasta,
            'cbte_nro' => (int) $detalle->CbteNro,
            'punto_venta' => $this->puntoVenta,
            'observaciones' => $this->extractObservaciones($detalle),
        ];
    }

    /**
     * Verifica un comprobante ya autorizado.
     */
    public function verificarComprobante(int $cbteTipo, int $cbteNro): array
    {
        $client = $this->getClient();

        $result = $client->FECompConsultar([
            'Auth' => $this->authArray(),
            'FeCompConsReq' => [
                'CbteTipo' => $cbteTipo,
                'CbteNro' => $cbteNro,
                'PtoVta' => $this->puntoVenta,
            ],
        ]);

        $this->checkErrors($result);

        $detalle = $result->FECompConsultarResult->ResultGet;

        return [
            'cae' => (string) $detalle->CAE,
            'cae_vencimiento' => (string) $detalle->CAEFchVto,
            'resultado' => (string) $detalle->Resultado,
            'fecha' => (string) $detalle->CbteFch,
            'imp_total' => (float) $detalle->ImpTotal,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function soapItems($value): array
    {
        if (is_array($value)) {
            if (!array_is_list($value)) {
                return [$value];
            }

            return array_values(array_filter($value, fn ($item) => is_object($item) || is_array($item)));
        }

        if (is_object($value)) {
            return [$value];
        }

        return [];
    }

    protected function soapField($item, string $field)
    {
        if (is_object($item)) {
            return $item->{$field} ?? null;
        }

        if (is_array($item)) {
            return $item[$field] ?? null;
        }

        return null;
    }

    /**
     * Verifica si la respuesta contiene errores de ARCA/AFIP.
     * Método genérico que detecta errores en cualquier respuesta WSFEv1.
     */
    protected function checkErrors($result, string $method = ''): void
    {
        $errors = [];
        $this->collectInformationalEvents($result);

        // Patrón 1: Errores genéricos en ResultGet (FEParamGetTiposCbte, etc.)
        if (isset($result->Errors) && isset($result->Errors->Err)) {
            foreach ($this->soapItems($result->Errors->Err) as $err) {
                $errors[] = "[{$this->soapField($err, 'Code')}] {$this->soapField($err, 'Msg')}";
            }
        }

        // Patrón 2: Errores en método específico (FECAESolicitarResult)
        if (isset($result->FECAESolicitarResult->Errors)) {
            foreach ($this->soapItems($result->FECAESolicitarResult->Errors->Err) as $err) {
                $errors[] = "[{$this->soapField($err, 'Code')}] {$this->soapField($err, 'Msg')}";
            }
        }

        // Patrón 3: Errores en FECompUltimoAutorizadoResult
        if (isset($result->FECompUltimoAutorizadoResult->Errors)) {
            foreach ($this->soapItems($result->FECompUltimoAutorizadoResult->Errors->Err) as $err) {
                $errors[] = "[{$this->soapField($err, 'Code')}] {$this->soapField($err, 'Msg')}";
            }
        }

        // Patrón 4: Errores en FECompConsultarResult
        if (isset($result->FECompConsultarResult->Errors)) {
            foreach ($this->soapItems($result->FECompConsultarResult->Errors->Err) as $err) {
                $errors[] = "[{$this->soapField($err, 'Code')}] {$this->soapField($err, 'Msg')}";
            }
        }

        // Patrón 5: Errores en FEParamGetCotizacionResult
        if (isset($result->FEParamGetCotizacionResult->Errors)) {
            foreach ($this->soapItems($result->FEParamGetCotizacionResult->Errors->Err) as $err) {
                $errors[] = "[{$this->soapField($err, 'Code')}] {$this->soapField($err, 'Msg')}";
            }
        }

        // Patrón 6: Observaciones en FECAESolicitarResult (no son errores fatales pero se registran)
        if (isset($result->FECAESolicitarResult->FeCabResp->Observaciones)) {
            foreach ($this->soapItems($result->FECAESolicitarResult->FeCabResp->Observaciones->Obs) as $obs) {
                $errors[] = "[{$this->soapField($obs, 'Code')}] {$this->soapField($obs, 'Msg')}";
            }
        }

        // Patrón 7: Búsqueda recursiva genérica para cualquier estructura no cubierta
        if (empty($errors)) {
            $this->checkErrorsRecursive($result, $errors);
        }

        if (!empty($errors)) {
            Log::error('ARCA/AFIP WSFE errors', ['method' => $method, 'errors' => $errors]);
            throw new Exception('ARCA/AFIP error: ' . implode('; ', $errors));
        }
    }

    /**
     * Búsqueda recursiva de errores en estructuras SOAP no mapeadas.
     */
    protected function checkErrorsRecursive($data, array &$errors): void
    {
        if (is_object($data)) {
            if ($this->soapField($data, 'Code') !== null && $this->soapField($data, 'Msg') !== null) {
                $errors[] = "[{$this->soapField($data, 'Code')}] {$this->soapField($data, 'Msg')}";
                return;
            }
            foreach ($data as $key => $child) {
                if (in_array($key, ['Events', 'Evts', 'Evt'], true)) {
                    continue;
                }
                $this->checkErrorsRecursive($child, $errors);
            }
        } elseif (is_array($data)) {
            if ($this->soapField($data, 'Code') !== null && $this->soapField($data, 'Msg') !== null) {
                $errors[] = "[{$this->soapField($data, 'Code')}] {$this->soapField($data, 'Msg')}";
                return;
            }
            foreach ($data as $key => $child) {
                if (in_array($key, ['Events', 'Evts', 'Evt'], true)) {
                    continue;
                }
                $this->checkErrorsRecursive($child, $errors);
            }
        }
    }

    public function pullInformationalEvents(): array
    {
        $events = $this->informationalEvents;
        $this->informationalEvents = [];

        return $events;
    }

    protected function collectInformationalEvents($data): void
    {
        if (is_object($data)) {
            foreach ($data as $key => $child) {
                if (in_array($key, ['Events', 'Evts'], true) && isset($child->Evt)) {
                    foreach ($this->soapItems($child->Evt) as $event) {
                        if ($this->soapField($event, 'Code') !== null && $this->soapField($event, 'Msg') !== null) {
                            $this->informationalEvents[] = [
                                'code' => (int) $this->soapField($event, 'Code'),
                                'message' => (string) $this->soapField($event, 'Msg'),
                            ];
                        }
                    }
                    continue;
                }

                $this->collectInformationalEvents($child);
            }
        } elseif (is_array($data)) {
            foreach ($data as $key => $child) {
                if (in_array($key, ['Events', 'Evts'], true) && isset($child['Evt'])) {
                    foreach ($this->soapItems($child['Evt']) as $event) {
                        if ($this->soapField($event, 'Code') !== null && $this->soapField($event, 'Msg') !== null) {
                            $this->informationalEvents[] = [
                                'code' => (int) $this->soapField($event, 'Code'),
                                'message' => (string) $this->soapField($event, 'Msg'),
                            ];
                        }
                    }
                    continue;
                }

                $this->collectInformationalEvents($child);
            }
        }
    }

    /**
     * Extrae observaciones de la respuesta.
     */
    protected function extractObservaciones($detalle): array
    {
        $observaciones = [];

        if (isset($detalle->Observaciones) && isset($detalle->Observaciones->Obs)) {
            foreach ($this->soapItems($detalle->Observaciones->Obs) as $obs) {
                $observaciones[] = [
                    'code' => (int) $this->soapField($obs, 'Code'),
                    'message' => (string) $this->soapField($obs, 'Msg'),
                ];
            }
        }

        return $observaciones;
    }
}
