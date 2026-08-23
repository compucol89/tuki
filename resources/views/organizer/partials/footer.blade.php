<footer class="footer py-4">
  <div class="container-fluid">
    <div class="d-block mx-auto">
      @php
        $footer_text = '';
        $date = Date('Y');
        if (!is_null($footerTextInfo)) {
            $footer_text = str_replace('{year}', $date, $footerTextInfo->copyright_text);
            $footer_text = preg_replace('/<p\b[^>]*>.*?(?:TAYRONA GROUP SAS|CUIT|WhatsApp|wa\.me).*?<\/p>/is', '', $footer_text) ?? $footer_text;
            $footer_text = trim($footer_text);
        }
      @endphp
      {!! $footer_text !!}
    </div>
  </div>
</footer>
