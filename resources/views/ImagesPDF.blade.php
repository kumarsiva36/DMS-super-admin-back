<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $pdfname }}</title>
    <style type="text/css">
        @font-face {
            font-family: 'poppins';
            src: url({{ storage_path('fonts/Poppins-Regular.ttf') }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }
        /* @font-face {
            font-family: 'poppins-semibold';
            src: url({{ storage_path('fonts/Poppins-SemiBold.ttf') }}) format("truetype");
            font-weight: 600;
            font-style: semibold;
        } */

        @font-face {
            font-family: 'arialuni';
            src: url({{ storage_path('fonts/arial_unicode.ttf') }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'arialuni';
            src: url({{ storage_path('fonts/arial_unicode.ttf') }}) format("truetype");
            font-weight: 600;
            font-style: semibold;
        }
        @font-face {
            font-family: 'notosansjp';
            src: url({{ storage_path('fonts/NotoSansJP-Regular.otf') }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }
        body {
            font-family: 'Poppins';
        }
        .page-break {
            page-break-after: always;
        }

    </style>

</head>



<body style="font-family: poppins,arialuni,notosansjp; font-size: 14px;">
    @if($pdf_header!='')
        <h3 style="font-size:16px; text-align:center">{{ $pdf_header }}</h3>
    @endif
    <div style="clear : both;"></div>
    @php $i=0; @endphp
    @foreach ($files as $file)
        @php $i++; @endphp
        <div style="text-align:{{ $alignment }}">
            @if($text_display=='before')
                <h3>{{ $text[$i-1]; }}</h3>
            @endif
            <img src="<?php echo $file; ?>" alt="Image" style="max-width: 100%">
            @if($text_display=='after')
                <h3>{{ $text[$i-1]; }}</h3>
            @endif
        </div>
        {{-- <p>&nbsp;</p> --}}
        <div style="clear : both;"></div>
        @if($display=='new' && count($files)> $i)
            <div class="page-break"></div>
        @endif
    @endforeach
    <footer>
        @if($pdf_footer==1)
        <script type="text/php">
            if (isset($pdf) ) {
               $font = $fontMetrics->getFont("Poppins", "bold");
               $pdf->page_text(28,815, "{{ $pdf_header }}", $font, 9, array(0, 0, 0));
               $pdf->page_text(490,815, "{{ date('d M Y') }}  {PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));
            }
        </script>
        @endif
    </footer>
</body>
</html>

