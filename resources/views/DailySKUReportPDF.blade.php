<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daily SKU Report</title>
    <style type="text/css">
        @font-face {
            font-family: 'poppins';
            src: url({{ storage_path('fonts/Poppins-Regular.ttf') }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'poppins-semibold';
            src: url({{ storage_path('fonts/Poppins-SemiBold.ttf') }}) format("truetype");
            font-weight: 600;
            font-style: semibold;
        }

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
            border: 1px solid #EFEFEF;
            border-collapse: collapse;
        }

        .mainTable td {
            border: 1px solid #EFEFEF;
            border-collapse: collapse;
        }

        .mainTable th {
            border: 1px solid #EFEFEF;
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

<body style="font-family: poppins,arialuni,notosansjp,poppins-semibold; font-size: 14px;">
    <div style="margin:25px 0;">
        <img src="{{ public_path() . '/images/dms-log-with-tag.png' }}"
            style="background-color: #FFFFFF; height: 98px; width:197px" />
        {{-- <img src="{{ asset('images/dms-log-with-tag.png') }}"
            style="background-color: #FFFFFF; height: 98px; width:197px" /> --}}
        <div style="float:right; font-size:25px; font-weight:600; color: #8C878D; ">
            <strong>{{ trans('WebSite.dailySKUReport') }}</strong>
            <div
                style=" background-color: #D1E7E4; font-size:16px; color: #178677; text-align:center;font-weight: 600;
            padding: 1px 3px 5px;">
                <img src="{{ public_path() . '/images/CalendarIcon.svg' }}" /> {{ date($dailyUpdates['dateFormat']) }}
                {{-- <img src="{{ asset('images/CalendarIcon.svg') }}" /> {{ date($dailyUpdates['dateFormat']) }} --}}
            </div>
        </div>
        <div style="margin: 5px 0;">
            @if (count($dailyUpdates['advFilter'])>0)
                <strong>{{ trans('WebSite.filter') }} : </strong>
                @if (array_key_exists("startDate",$dailyUpdates['advFilter']) && array_key_exists("endDate",$dailyUpdates['advFilter']))
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.StartDate') .": ".$dailyUpdates['advFilter']['startDate'] }}</strong>
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.EndDate') .": ".$dailyUpdates['advFilter']['endDate'] }}</strong>
                @endif
                @if (array_key_exists("prodType",$dailyUpdates['advFilter']))
                    @if ($dailyUpdates['advFilter']['prodType'] === "Cut")
                        <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.'.$dailyUpdates['advFilter']['prodType'].'ting') }}</strong>
                    @else
                        <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.'.$dailyUpdates['advFilter']['prodType'].'ing') }}</strong>
                    @endif
                @endif
                @if (array_key_exists("color",$dailyUpdates['advFilter']))
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.color') }} :
                        <?php $colorLength = count($dailyUpdates['advFilter']['color']); $iColor=1;
                        ?>
                    @foreach ( $dailyUpdates['advFilter']['color'] as $color)
                        @if ($iColor < $colorLength)
                            {{ $color." , " }}
                        @else
                            {{ $color }}
                        @endif
                        <?php $iColor++; ?>
                    @endforeach
                    </strong>
                @endif
                @if (array_key_exists("size",$dailyUpdates['advFilter']))
                    <strong style="background-color: #E8E8E8;color:#606060;padding:1px 3px 3px;">{{ trans('WebSite.size') }} :
                        <?php $sizeLength = count($dailyUpdates['advFilter']['color']); $iSize=1;
                        ?>
                    @foreach ( $dailyUpdates['advFilter']['size'] as $size)
                        @if ($iSize < $sizeLength)
                            {{ $size." , " }}
                        @else
                            {{ $size }}
                        @endif
                        <?php $iSize++; ?>
                    @endforeach
                    </strong>
                @endif
            @endif
        </div>
        <div style="margin: 25px 0;">
            <div style="width:33%;margin : 5px; float:left;border-radius : 5px; height:auto;">
                <table style="margin: 0;padding: 0; width:100%;" class="tableType">
                    <tr style="background-color: #C4E1DD;">
                        <td style="width: 20%;">
                            <img src="{{ public_path() . '/images/OrderIconColored.png' }}" style="padding:15px 10px" />
                            {{-- <img src="{{ asset('images/OrderIconColored.png') }}" style="padding:15px 10px" /> --}}
                        </td>
                        <td style="line-height: 1;">
                            <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                <strong>{{ trans('WebSite.Order') }}</strong></p>
                            <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px; ">
                                {{ $dailyUpdates['orderNo'] }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:4px;"></td>
                    </tr>
                    <tr style="background-color: #C4E1DD;">
                        <td style="width: 10%;font-family: poppins,arialuni,notosansjp;">
                            <img src="{{ public_path() . '/images/StyleIconColored.png' }}" style="padding:15px 10px" />
                            {{-- <img src="{{ asset('images/StyleIconColored.png') }}" style="padding:15px 10px" /> --}}
                        </td>
                        <td style="line-height: 1;">
                            <p style="padding-left: 0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                <strong>{{ trans('WebSite.Style') }}</strong></p>
                            <p style="padding-left: 0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;">
                                {{ $dailyUpdates['styleNo'] }}</p>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:31%; float:left;margin : 5px;border-radius : 5px; height:auto;">
                <table style="margin: 0;padding: 0; width:100%;" class="tableType">
                    @if (in_array('factory', array_keys($dailyUpdates)))
                        <tr style="background-color: #C4E1DD;">
                            <td style="width: 20%;font-family: poppins,arialuni,notosansjp;">
                                <img src="{{ public_path() . '/images/FactoryColored.png' }}" style="padding:16px 10px" />
                                {{-- <img src="{{ asset('images/FactoryColored.png') }}" style="padding:15px 10px" /> --}}
                            </td>
                            <td style="line-height: 1;">
                                <p style="padding-left:0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.Factory') }}</strong></p>
                                <p style="padding-left:0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;">
                                    {{ $dailyUpdates['factory'] }}</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding:4px;"></td>
                        </tr>
                    @endif
                    @if (in_array('pcu', array_keys($dailyUpdates)))
                        <tr style="background-color: #C4E1DD;">
                            <td style="width: 20%;font-family: poppins,arialuni,notosansjp;">
                                <img src="{{ public_path() . '/images/PCUColored.png' }}" style="padding:15px 10px" /></td>
                                {{-- <img src="{{ asset('images/PCUColored.png') }}" style="padding:15px 10px" /></td> --}}
                            <td style="line-height: 1;">
                                <p style="padding-left:0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.PCU') }}</strong></p>
                                <p style="padding-left:0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;">
                                    {{ $dailyUpdates['pcu'] }}</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding:4px;"></td>
                        </tr>
                    @endif
                    @if (in_array('buyer', array_keys($dailyUpdates)))
                        <tr style="background-color: #C4E1DD;">
                            <td style="width: 20%;font-family: poppins,arialuni,notosansjp;">
                                <img src="{{ public_path() . '/images/BuyerColored.png' }}" style="padding:15px 10px" />
                                {{-- <img src="{{ asset('images/BuyerColored.png') }}" style="padding:15px 10px" /> --}}
                            </td>
                            <td style="line-height: 1;">
                                <p style="padding-left:0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                    <strong>{{ trans('WebSite.Buyer') }}</strong></p>
                                <p style="padding-left:0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;">
                                    {{ $dailyUpdates['buyer'] }}</p>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
            <div style="width:32%;margin : 5px; float:right;border-radius : 5px; height:auto;">
                <table style="margin: 0;padding: 0; width:100%;" class="tableType">
                    <tr style="background-color: #C4E1DD;">
                        <td style="width: 10%;font-family: poppins,arialuni,notosansjp;">
                            <img src="{{ public_path() . '/images/CalendarIcon.svg' }}" style="padding:21px 10px"/>
                        </td>
                        <td style="line-height: 1;">
                            <p style="padding-left:0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                <strong>{{ trans('WebSite.StartDate') }}</strong></p>
                            <p style="padding-left:0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;">
                                {{ date($dailyUpdates['dateFormat'],strtotime($dailyUpdates['startDate'])) }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:4px;"></td>
                    </tr>
                    <tr style="background-color: #C4E1DD;">
                        <td style="width: 10%;font-family: poppins,arialuni,notosansjp;">
                            <img src="{{ public_path() . '/images/CalendarIcon.svg' }}" style="padding:21px 10px"/>
                        </td>
                        <td style="line-height: 1; ">
                            <p style="padding-left:0px;color:#178677;margin-bottom:0px;margin-top:2px;font-family: poppins,arialuni,notosansjp;">
                                <strong>{{ trans('WebSite.EndDate') }}</strong></p>
                            <p style="padding-left:0px;color:#178677;word-wrap: break-word;margin-top:0px;margin-bottom:5px;">
                                {{ date($dailyUpdates['dateFormat'],strtotime($dailyUpdates['endDate'])) }}</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div style="clear : both;"></div>
    <?php
    $cut_data= $sew_data= $pack_data ='';
    $cut_actual_total=$sew_actual_total=$pack_actual_total =0;
    $cut_target_total=$sew_target_total=$pack_target_total =0;
    $cut_excess_total=$sew_excess_total=$pack_excess_total =0;
    $cut_short_total=$sew_short_total=$pack_short_total =0;
    $cut_diff_total=$sew_diff_total=$pack_diff_total = 0;
    ?>
    @foreach ($dailyUpdates['prodData'] as $data)
        <?php
        if(isset($data['Cut_actual'])){
            $cutActual = 0;
            $cutSKU = '';
            foreach ($data['Cut'] as $key => $value) {
                $cutActual += $value;
                $cutSKU.=   '<tr>
                                <td style="font-family: poppins,arialuni,notosansjp;border: none;"><strong>'.$key.'</strong></td>
                                <td style="font-family: poppins,arialuni,notosansjp;border: none;"> : </td>
                                <td style="font-family: poppins,arialuni,notosansjp;border: none;">'.$value.'</td>
                            </tr>';
            }

            $cut_actual_total += $cutActual;
            $cut_target_total += $data['Cut_target'];
            $difference = $cutActual - $data['Cut_target'];
            if ($difference == 0)
                $diff='0/0';
            elseif ($difference < 0){
                $diff= '0/'.abs($difference) ;
                $cut_short_total += abs($difference);
            }
            elseif ($difference > 0){
                $diff= $difference.'/0' ;
                $cut_excess_total += abs($difference);
            }
            $cut_data.='<tr>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">'.$data['sku_date'].'</td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">'.$data['Cut_target'].'</td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><table style="border:none;">'.$cutSKU.
                                '</table></td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">'.$diff.'</td>
                        </tr>';


        }
        if(isset($data['Sew_actual'])){

            $sewActual = 0;
            $sewSKU = '';
            foreach ($data['Sew'] as $key => $value) {
                $sewActual += $value;
                $sewSKU.=   '<tr>
                                <td style="font-family: poppins,arialuni,notosansjp;border: none;"><strong>'.$key.'</strong></td>
                                <td style="font-family: poppins,arialuni,notosansjp;border: none;"> : </td>
                                <td style="font-family: poppins,arialuni,notosansjp;border: none;">'.$value.'</td>
                            </tr>';
            }

            $sew_actual_total += $sewActual;
            $sew_target_total += $data['Sew_target'];
            $difference = $sewActual - $data['Sew_target'];
            if ($difference == 0)
                $diff='0/0';
            elseif ($difference < 0){
                $diff= '0/'.abs($difference) ;
                $sew_short_total += abs($difference);
            }
            elseif ($difference > 0){
                $diff= $difference.'/0' ;
                $sew_excess_total += abs($difference);
            }


            $sew_data.='<tr>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">'.$data['sku_date'].'</td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">'.$data['Sew_target'].'</td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><table style="border:none;">'
                                .$sewSKU.'</table></td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">'.$diff.'</td>
                        </tr>';
        }
        if(isset($data['Pack_actual'])){

            $packActual = 0;
            $packSKU = '';
            foreach ($data['Pack'] as $key => $value) {
                $packActual += $value;
                $packSKU.=  '<tr>
                                <td style="font-family: poppins,arialuni,notosansjp;border: none;"><strong>'.$key.'</strong></td>
                                <td style="font-family: poppins,arialuni,notosansjp;border: none;"> : </td>
                                <td style="font-family: poppins,arialuni,notosansjp;border: none;">'.$value.'</td>
                            </tr>';
            }

            $pack_actual_total += $packActual;
            $pack_target_total += $data['Pack_target'];
            $difference = $packActual - $data['Pack_target'];

            if ($difference == 0)
                $diff='0/0';
            elseif ($difference < 0){
                $diff= '0/'.abs($difference);
                $pack_short_total += abs($difference);
            }
            elseif ($difference > 0){
                $diff= $difference.'/0';
                $pack_excess_total += abs($difference);
            }

            $pack_data.='<tr>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">'.$data['sku_date'].'</td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">'.$data['Pack_target'].'</td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><table style="border:none;">'.$packSKU
                                .'</table></td>
                            <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">'.$diff.'</td>
                        </tr>';
        }
        ?>
    @endforeach
    <?php
        $cut_diff_total = $cut_excess_total - $cut_short_total;
        $sew_diff_total = $sew_excess_total - $sew_short_total;
        $pack_diff_total = $pack_excess_total - $pack_short_total;
    ?>
    <br/>
    @if ($cut_data!="")
        <img src="{{ public_path() . '/images/GreenCutting.png' }}" style="margin-top:5px; float: left;" />
        <p style="color:#178677;font-weight:400; font-family: poppins,arialuni,notosansjp; float: left;margin-top:7px;">
            &nbsp;&nbsp;<strong>{{ trans('WebSite.Cutting') }}</strong></p>
            <div style="clear : both;"></div>
        <table style="width: 100%;border-collapse: collapse;font-family: poppins,arialuni,notosansjp;"
        cellspacing="1px" class="mainTable">
            <tr style="background-color: #C4E1DD; color: #178677; font-weight:500; font-family: poppins,arialuni,notosansjp;">
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.date') }}</strong></td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.target') }}</strong></td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.actual') }}</strong></td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.excess')."/".trans('WebSite.short') }}</strong></td>
            </tr>
            {!! $cut_data !!}
            <tr>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.Total') }}</strong></td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $cut_target_total }}</td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $cut_actual_total }}</td>
                @if ($cut_diff_total === 0)
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">0/0</td>
                @elseif ($cut_diff_total > 0)
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $cut_diff_total."/0" }}</td>
                @elseif ($cut_diff_total < 0)
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ "0/".abs($cut_diff_total) }}</td>
                @endif
            </tr>
        </table>
        <br/>
    @endif
    @if ($sew_data != "")
        <img src="{{ public_path() . '/images/GreenSewing.png' }}" style="margin-top: 5px; float: left;"/>
        <p style="color:#178677;font-weight:400; font-family: poppins,arialuni,notosansjp; float: left;margin-top:7px;">
            &nbsp;&nbsp;<strong style="">{{ trans('WebSite.Sewing') }}</strong></p>
            <div style="clear : both;"></div>
            <table style="width: 100%;border-collapse: collapse;font-family: poppins,arialuni,notosansjp;"
            cellspacing="1px" class="mainTable">

            <tr style="background-color: #C4E1DD; color: #178677; font-weight:500; font-family: poppins,arialuni,notosansjp;">
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ trans('WebSite.date') }}</td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ trans('WebSite.target') }}</td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ trans('WebSite.actual') }}</td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ trans('WebSite.excess')."/".trans('WebSite.short') }}</td>
            </tr>
            {!! $sew_data !!}
            <tr>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.Total') }}</strong></td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $sew_target_total }}</td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $sew_actual_total }}</td>
                @if ($sew_diff_total === 0)
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">0/0</td>
                @elseif ($sew_diff_total > 0)
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $sew_diff_total."/0" }}</td>
                @elseif ($sew_diff_total < 0)
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ "0/".abs($sew_diff_total) }}</td>
                @endif
            </tr>
        </table>
        <br/>
    @endif
    @if ($pack_data!="")
        <img src="{{ public_path() . '/images/GreenPacking.png' }}"
        style="margin-top:5px; float: left;" />
        <p style="color:#178677;font-weight:400; font-family: poppins,arialuni,notosansjp; float: left;margin-top:7px;">
            &nbsp;&nbsp;<strong>{{ trans('WebSite.Packing') }}</strong></p>
            <div style="clear : both;"></div>
            <table style="width: 100%;border-collapse: collapse;font-family: poppins,arialuni,notosansjp;"
            cellspacing="1px" class="mainTable">
            <tr style="background-color: #C4E1DD; color: #178677; font-weight:500; font-family: poppins,arialuni,notosansjp;">
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ trans('WebSite.date') }}</td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ trans('WebSite.target') }}</td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ trans('WebSite.actual') }}</td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ trans('WebSite.excess')."/".trans('WebSite.short') }}</td>
            </tr>
            {!! $pack_data !!}
            <tr>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;"><strong>{{ trans('WebSite.Total') }}</strong></td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $pack_target_total }}</td>
                <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $pack_actual_total }}</td>
                @if ($pack_diff_total === 0)
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">0/0</td>
                @elseif ($pack_diff_total > 0)
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ $pack_diff_total."/0" }}</td>
                @elseif ($pack_diff_total < 0)
                    <td style="padding : 5px 5px 10px 20px; font-family: poppins,arialuni,notosansjp;">{{ "0/".abs($pack_diff_total) }}</td>
                @endif
            </tr>
        </table>
    @endif
    <footer style="margin-top: 15px;width: 100%;background-color: #D1E7E4; font-size:14px; color: #178677; text-align:center;
            padding: 1px 3px 5px;">
            <strong>
                {!! trans('WebSite.PDFFooter',['date'=>$dailyUpdates['endDate']]) !!}
            </strong>
    </footer>
</body>

</html>
