<?php
/**
 * Created by PhpStorm.
 * User: Sihuo
 * Date: 2017/5/10
 * Time: 13:27
 */

?>
<div id="main" style="height: 700px;margin:0 auto;padding:0;width:95%;"></div>

<script>
    var myChart = echarts.init(document.getElementById('main'));
    var dataMap = {};
    var origin = eval('(' + '<?= $data['series']?>' + ')');
    console.log(origin)
    function dataFormatter(obj) {
        var pList = eval('(' + '<?= $data['xAxis']?>' + ')');
        var temp;
        for (var year = '<?= $data['min']?>'; year <= '<?= $data['max']?>'; year++) {
            var max = 0;
            var sum = 0;
            temp = obj[year];
            for (var i = 0, l = temp.length; i < l; i++) {
                max = Math.max(max, temp[i]);
                sum += temp[i];
                obj[year][i] = {
                    name: pList[i],
                    value: temp[i]
                }
            }
            obj[year + 'max'] = Math.floor(max / 100) * 100;
            obj[year + 'sum'] = sum;
        }
        return obj;
    }

    dataMap.dataGDP = dataFormatter(origin.gdp);

    dataMap.dataPI = dataFormatter(origin.one_pro);

    dataMap.dataSI = dataFormatter(origin.two_pro);

    dataMap.dataTI = dataFormatter(origin.three_pro);

    dataMap.dataEstate = dataFormatter(origin.house);

    dataMap.dataFinancial = dataFormatter(origin.finance);

    dataMap.other = dataFormatter(origin.other);



    option = {
        baseOption: {
            timeline: {
                // y: 0,
                axisType: 'category',
                // realtime: false,
                // loop: false,
                autoPlay: false,
                // currentIndex: 2,
                playInterval: 1000,
                // controlStyle: {
                //     position: 'left'
                // },
                data: <?= $data['baseX']?>,
                label: {
                    formatter: function (s) {
                        return (new Date(s)).getFullYear();
                    }
                }
            },
            title: {
                subtext: '数据纯属虚构'
            },
            tooltip: {},
            legend: {
                x: 'right',
                data: ['第一产业', '第二产业', '第三产业', 'GDP', '金融', '房地产', '其他'],
                selected: {
                    'GDP': false, '金融': false, '房地产': false
                }
            },
            calculable: true,
            grid: {
                top: 80,
                bottom: 100
            },
            xAxis: [
                {
                    'type': 'category',
                    'axisLabel': {'interval': 0},
                    'data': <?=$data['xAxis']?>,
                    splitLine: {show: false}
                }
            ],
            yAxis: [
                {
                    type: 'value',
                    name: 'GDP（亿元）',
                    // max: 53500
                    max: 30000
                }
            ],
            series: [
                {name: 'GDP', type: 'bar'},
                {name: '金融', type: 'bar'},
                {name: '房地产', type: 'bar'},
                {name: '第一产业', type: 'bar'},
                {name: '第二产业', type: 'bar'},
                {name: '第三产业', type: 'bar'},
                {
                    name: 'GDP占比',
                    type: 'pie',
                    center: ['75%', '35%'],
                    radius: '28%'
                },
                {name: '其他', type: 'bar'},
            ]
        },
        options: [
            <?php foreach (json_decode($data['baseX']) as  $k => $year): ?>
            {
                title: {text: '<?=$year?>全国宏观经济指标'},
                series: [
                    {data: dataMap.dataGDP['<?=$year?>']},
                    {data: dataMap.dataFinancial['<?=$year?>']},
                    {data: dataMap.dataEstate['<?=$year?>']},
                    {data: dataMap.dataPI['<?=$year?>']},
                    {data: dataMap.dataSI['<?=$year?>']},
                    {data: dataMap.dataTI['<?=$year?>']},
                    {
                        data: [
                            {name: '第一产业', value: dataMap.dataPI['<?=$year?>sum']},
                            {name: '第二产业', value: dataMap.dataSI['<?=$year?>sum']},
                            {name: '第三产业', value: dataMap.dataTI['<?=$year?>sum']}
                        ]
                    },
                    {data: dataMap.other['<?=$year?>']},
                ]
            },
            <?php endforeach;?>
        ]
    };
    console.log(option)
    myChart.setOption(option);
</script>
