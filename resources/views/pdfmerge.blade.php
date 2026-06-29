<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PDF Merge</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */
            html{line-height:1.15;-webkit-text-size-adjust:100%}
            body{margin:0}
            html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}
            .bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}
            .container{ max-width: 1100px; margin: auto; padding: 15px;}
            .txt_box { width: 350px; padding: 10px 5px; border: 1px solid #222}
            .btn_submit{ padding: 10px 20px; font-size:15px; background: #0d6efd; color: #ffffff; border-radius: 5px; font-weight: bold; letter-spacing: 1px;}
            .btn_submit:hover{cursor: pointer;background: #0dcaf0}
        </style>

        <style>
            body {
                font-family: 'Nunito';
            }
        </style>
    </head>
    <body class="antialiased">

        <div class="container bg-gray-100">
            <h1>PDF Merge</h1>
            <form action="pdfmergesubmit" enctype="multipart/form-data" name="pdfmerge" method="post">
                @csrf
                <table cellpadding="5" cellspacing="20" width="100%">
                    <tr>
                        <td width="20%">PDF 1</td>
                        <td><input type="file" name="pdf[]" accept="application/pdf" required></td>
                    </tr>
                    <tr>
                        <td>PDF 2</td>
                        <td><input type="file" name="pdf[]" accept="application/pdf" required></td>
                    </tr>
                    <tr>
                        <td>PDF 3</td>
                        <td><input type="file" name="pdf[]" accept="application/pdf"></td>
                    </tr>
                    <tr>
                        <td>PDF 4</td>
                        <td><input type="file" name="pdf[]" accept="application/pdf"></td>
                    </tr>
                    <tr>
                        <td>PDF 5</td>
                        <td><input type="file" name="pdf[]" accept="application/pdf"></td>
                    </tr>
                    <tr>
                        <td>Merged PDF Name</td>
                        <td><input type="text" name="pdf_name" class="txt_box"></td>
                    </tr>
                    <tr>
                        <td>PDF Header</td>
                        <td><input type="text" name="pdf_header" class="txt_box"></td>
                    </tr>
                    <tr>
                        <td>PDF Footer</td>
                        <td><input type="radio" name="pdf_footer" value="1" checked> Yes &nbsp;&nbsp; <input type="radio" name="pdf_footer" value="0"> No</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" value="Merge" class="btn_submit"></td>
                    </tr>
                </table>
            </form>
        </div>
    </body>
</html>
