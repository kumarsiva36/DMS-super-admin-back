<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PDF to Text</title>

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
            <h1>PDF To Text conversion</h1>
            <div class="py-12">
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <form action="pdftotext_submit" enctype="multipart/form-data"
                                method="POST">
                                @csrf
                                    <div
                                        class="relative h-40 rounded-lg border-dashed border-2 border-gray-200 bg-white flex justify-center items-center hover:cursor-pointer">
                                        <div class="absolute">
                                            <div class="flex flex-col items-center "> <i
                                                    class="fa fa-cloud-upload fa-3x text-gray-200"></i>
                                                <span class="block text-gray-400 font-normal">Select your PDF file</span>
                                            </div>
                                        </div>
                                        <input type="file" class="h-full w-full opacity-0" name="file" >
                                    </div>
                                </div>
                                <div class="mt-10 text-center pb-3"><br>
                                    <button type="submit"
                                        class="btn_submit">
                                        Convert
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
