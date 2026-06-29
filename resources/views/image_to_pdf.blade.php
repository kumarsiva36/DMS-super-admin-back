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
            body{margin:0; font-family: 'Nunito';}
            html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}
            .bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}
            .container{ max-width: 1100px; margin: auto; padding: 15px;}
            .txt_box { width: 350px; padding: 10px 5px; border: 1px solid #222}
            .btn_submit{ padding: 10px 20px; font-size:15px; background: #0d6efd; color: #ffffff; border-radius: 5px; font-weight: bold; letter-spacing: 1px;}
            .btn_submit:hover{cursor: pointer;background: #0dcaf0}
            a {text-decoration: none;font-size: 20px;background: #0d6efd;color: #ffffff;border-radius: 50%;padding: 7px 15px;}
        </style>
        <script type="text/javascript">
        function fnAddRow(){
            const count = document. querySelectorAll('input[type=file]').length+1;
            const boxWrapper = document.getElementById("mainTable");  
            const boxWrapper1 = document.getElementById("box"+(count-1));
            const box = document.createElement("div");
            box.innerHTML = '<table cellpadding="5" cellspacing="20" width="100%"><tr><td width="20%">Image '+count+'</td><td width="35%"><input type="file" name="image[]" accept="image/*"></td></tr><tr><td width="20%">Image text</td><td><input type="text" name="text[]" class="txt_box"></td><td><a href="javascript:void(0)" onclick="fnRemoveRow('+count+')">-</a></td></tr></table>';

            //box.style.backgroundColor = "orange";
            box.classList.add("box"+count);
            box.setAttribute('id',"box"+count)

            if(count <7)
                boxWrapper.after(box);
            else{
                boxWrapper1.after(box);
            }
        }
        function fnRemoveRow(val){
            document.querySelector(".box"+val).remove();
        }
        </script>
    </head>
    <body class="antialiased">

        <div class="container bg-gray-100">
            <h1>Images to PDF</h1>
            <form action="image-to-pdf-submit" enctype="multipart/form-data" name="image-to-pdf" method="post">
                @csrf
                <table cellpadding="5" cellspacing="20" width="100%" id="mainTable">
                    <tr>
                        <td width="20%">Image 1*</td>
                        <td width="35%"><input type="file" name="image[]" accept="image/*" required></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="20%">Image text</td>
                        <td><input type="text" name="text[]" class="txt_box"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Image 2</td>
                        <td><input type="file" name="image[]" accept="image/*" ></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="20%">Image text</td>
                        <td><input type="text" name="text[]" class="txt_box"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Image 3</td>
                        <td><input type="file" name="image[]" accept="image/*"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="20%">Image text</td>
                        <td><input type="text" name="text[]" class="txt_box"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Image 4</td>
                        <td><input type="file" name="image[]" accept="image/*"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="20%">Image text</td>
                        <td><input type="text" name="text[]" class="txt_box"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Image 5</td>
                        <td><input type="file" name="image[]" accept="image/*"></td>
                        <td></td>
                    </tr>
                    {{-- <tr>
                        <td width="20%">Image text</td>
                        <td><input type="text" name="text[]" class="txt_box"></td>
                    </tr>
                    <tr>
                        <td>Image 6</td>
                        <td><input type="file" name="image[]" accept="image/*"></td>
                    </tr>
                    <tr>
                        <td width="20%">Image text</td>
                        <td><input type="text" name="text[]" class="txt_box"></td>
                    </tr>
                    <tr>
                        <td>Image 7</td>
                        <td><input type="file" name="image[]" accept="image/*"></td>
                    </tr>
                    <tr>
                        <td width="20%">Image text</td>
                        <td><input type="text" name="text[]" class="txt_box"></td>
                    </tr>
                    <tr>
                        <td>Image 8</td>
                        <td><input type="file" name="image[]" accept="image/*"></td>
                    </tr>
                    <tr>
                        <td width="20%">Image text</td>
                        <td><input type="text" name="text[]" class="txt_box"></td>
                    </tr>
                    <tr>
                        <td>Image 9</td>
                        <td><input type="file" name="image[]" accept="image/*"></td>
                    </tr>
                    <tr>
                        <td width="20%">Image text</td>
                        <td><input type="text" name="text[]" class="txt_box"></td>
                    </tr>
                    <tr>
                        <td>Image 10</td>
                        <td><input type="file" name="image[]" accept="image/*"></td>
                    </tr> --}}
                    <tr>
                        <td width="20%">Image text</td>
                        <td><input type="text" name="text[]" class="txt_box"></td>
                        <td><a href="javascript:void(0)" onclick="fnAddRow()" title="Add More Images">+</a></td>
                    </tr>
                </table>
                <table cellpadding="5" cellspacing="20" width="100%">
                    <tr>
                        <td width="20%">Image text Display</td>
                        <td><input type="radio" name="text_display" value="before" checked> Before Image
                            <input type="radio" name="text_display" value="after" > After Image
                        </td>
                    </tr>
                    <tr>
                        <td>Alignment</td>
                        <td><input type="radio" name="alignment" value="left" checked> Left
                            <input type="radio" name="alignment" value="right" > Right
                            <input type="radio" name="alignment" value="center" > Center
                        </td>
                    </tr>
                    <tr>
                        <td>Image Display</td>
                        <td><input type="radio" name="display" value="auto" checked> Auto
                            <input type="radio" name="display" value="new" > New page
                        </td>
                    </tr>
                    <tr>
                        <td>PDF Name</td>
                        <td><input type="text" name="pdf_name" class="txt_box"></td>
                    </tr>
                    <tr>
                        <td>PDF Title</td>
                        <td><input type="text" name="pdf_header" class="txt_box"></td>
                    </tr>
                    <tr>
                        <td>PDF Footer</td>
                        <td><input type="radio" name="pdf_footer" value="1" checked> Yes &nbsp;&nbsp; <input type="radio" name="pdf_footer" value="0"> No</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="reset" value="Clear" class="btn_submit">&nbsp;&nbsp;&nbsp;
                            <input type="submit" value="Create PDF" class="btn_submit"></td>
                    </tr>
                </table>
            </form>
        </div>
    </body>
</html>
