<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Production Report</title>
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

        .mainTable table {
            border: 1px solid #e0e0e0;
            border-collapse: collapse;
        }

        .mainTable td {
            border: 1px solid #e0e0e0;
            border-collapse: collapse;
            vertical-align: top;
        }

        .mainTable th {
            border: 1px solid #e0e0e0;
            border-collapse: collapse;
        }

        .page-break {
            page-break-after: always;
        }

        .tableType td p {
            word-break: break-word !important;
        }

        .tableType {
            border-collapse: collapse;
        }
    </style>
    <script type="text/php">
            $x = 250;
            $y = 10;
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $font = null;
            $size = 14;
            $color = array(255,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    </script>
</head>

<?php
    function IsJson($str)
    {
        try{
            $json = json_decode($str);
            return true;
        }catch(Exception $e){
            return false;
        }
    }
?>

<body style="font-family: poppins,arialuni,notosansjp; font-size: 14px;">
    <div >
        <table width="100%"  cellpadding="5" cellspacing="0" class="" >
            <tr>
                <td width="15%">
                        <img src="{{ public_path() . '/images/dms-log-with-tag.png' }}"
                            style="background-color: #FFFFFF; height: 40px;"  />
                </td>
                <td >
                    <div style="vertical-align:middle;text-align:center; font-size:18px; font-weight:600; color: #8C878D; ">
                    <strong>{{ trans('WebSite.fabric_log') }} </strong>
                    </div>
                </td>
                <td width="12%">
                    <div style="vertical-align:middle;text-align:right; font-size:12px; font-weight:600; color: #8C878D; ">
                        {{ date('d M Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div style="clear : both;"></div>
    <div >
        {{-- <img src="{{ public_path() . '/images/dms-log-with-tag.png' }}"
            style="background-color: #FFFFFF; height: 98px; width:197px" />
        <div style="float:right; font-size:25px; font-weight:600; color: #8C878D; ">
            <strong>{{ trans('WebSite.fabric_log') }}</strong>
            <div
                style=" background-color: #D1E7E4; font-size:16px; color: #178677; text-align:center;font-weight: 600;
            padding: 1px 3px 5px;">
                <img src="{{ public_path() . '/images/CalendarIcon.svg' }}" /> {{ date('d M Y') }}
            </div>
        </div> --}}
        <div style="margin: 5px 0;">
            @if ($request)
                <strong>{{ trans('WebSite.filter') }} : </strong>
                @if (isset($request->company_id) && $request->company_id !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.companyName') .": ".$result[0]['company_name'] }}</strong>
                @endif
                {{-- @if (isset($request->workspace_id) && $request->workspace_id !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.workSpace') .": ".$result[0]['name'] }}</strong>
                @endif --}}
                @if (isset($request->user_id) && $request->user_id !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.user') .": ".$result[0]['user'] }}</strong>
                @endif
                @if (isset($request->staff_id) && $request->staff_id !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.staff') .": ".$result[0]['staffname'] }}</strong>
                @endif
                @if (isset($request->inquiry_id) && $request->inquiry_id !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.inquiry_id') .": IN-".$result[0]['inquiry_id'] }}</strong>
                @endif
                @if (isset($request->start_date) && $request->start_date !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.StartDate') .": ".$request->start_date }}</strong>
                @endif
                @if (isset($request->end_date) && $request->end_date !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.EndDate') .": ".$request->end_date }}</strong>
                @endif
                @if (isset($request->action) && $request->action !='')
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.action') .": ".$request->action }}</strong>
                @endif
            @endif
        </div>

    </div>
    <div style="clear : both;"></div>

    <table style="width: 100%;border-collapse: collapse;font-family: poppins,arialuni,notosansjp;"
        cellspacing="1px" class="mainTable">
            <tr style="background-color: #f0efef; color: #000000; font-weight:500; font-family: poppins,arialuni,notosansjp;">
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.date') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.inquiry_id') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.companyName') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.action') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.before') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.after') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.staff') }}</strong></td>
                <td style="padding : 5px ; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.user') }}</strong></td>

            </tr>
            @foreach ($result as $res)
            <tr>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['created_date']}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>IN-{{$res['inquiry_id']}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['company_name']}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{ucfirst($res['action'])}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;word-break: break-word;"><strong>
                    {!! ($res['before_values']) ? str_replace(',',"<br>",$res['before_values']) : "-" !!}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px; word-break: break-word;"><strong>
                    {!! ($res['after_values']) ? str_replace(',',"<br>",$res['after_values']) : "-" !!}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['user']}}</strong></td>
                <td style="padding:5px; font-family: poppins,arialuni,notosansjp; font-size: 12px;"><strong>{{$res['staffname']}}</strong></td>
            </tr>
            @endforeach
    </table>
    <footer>
        <script type="text/php">
            if (isset($pdf)) {
               $font = $fontMetrics->getFont("Poppins", "bold");
               $pdf->page_text(28,815, "{{ trans('WebSite.fabric_log') }}", $font, 9, array(0, 0, 0));
               $pdf->page_text(490,815, "{{ date('d M Y') }}  {PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));
            }
        </script>
    </footer>
</body>
</html>
