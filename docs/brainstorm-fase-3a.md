# Brainstorm Fase 3A: Mask alpha channel

## Contexto

El modo hibrido de imagenes preserva el flyer original componiendolo de nuevo sobre
la salida de OpenAI. El problema observado en produccion fue visual: la extension
del fondo duplico jugadores a los costados. La hipotesis principal es que el mask
actual no tiene canal alpha y OpenAI no recibe una frontera clara entre area editable
y area protegida.

## Convencion del mask

La documentacion oficial de OpenAI para edicion de imagenes indica que la imagen a
editar y el mask deben tener el mismo formato y tamano, y que el mask debe contener
un canal alpha. Tambien muestra el flujo de convertir un mask blanco/negro a RGBA y
usar el propio mask como alpha.

Decision:
- Area protegida del flyer: alpha `0` (opaca).
- Area editable fuera del flyer: alpha `127` (totalmente transparente en GD).
- Guardado PNG con `imagesavealpha($mask, true)`.

## Hard edge vs soft edge

Decision: usar borde duro en esta fase.

Motivo: es el cambio minimo, testeable y reversible. Un borde suave podria mejorar
la mezcla visual, pero tambien agrega una zona ambigua donde OpenAI podria tocar el
flyer o generar artefactos. Si el alpha mask duro funciona, el soft edge queda como
mejora futura.

## Flyer centrado y formatos actuales

Los formatos actuales son `1024x1024` y `1536x1024`. No hay formato vertical 9:16.
La colocacion se calcula con `containedPlacement`, por lo que el flyer queda centrado
y el area editable es todo lo que queda fuera del rectangulo del flyer, sean laterales
o bandas arriba/abajo.

## Feature flag y rollback

Se agrega `AI_IMAGES_USE_ALPHA_MASK=true` como flag de transicion. Si el alpha mask
produce una regresion visual o OpenAI lo interpreta mal, se puede poner en `false`
para volver al mask opaco anterior sin redeploy.

## Hallazgo input_fidelity

La documentacion oficial indica que `input_fidelity` debe omitirse para `gpt-image-2`.
Decision: incluir la remocion en Fase 3A como cambio complementario de bajo riesgo,
con test que asegure que el multipart ya no envia ese parametro.

## Fuera de alcance

- No tocar `BlurExtendService`.
- No tocar `GenerateAiImageJob::hybridValidationScore`.
- No tocar `compositeOriginalOnTop`.
- No agregar migraciones ni dependencias.
- No hacer requests reales a OpenAI.
