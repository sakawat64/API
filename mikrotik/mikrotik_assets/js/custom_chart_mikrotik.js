$(document).ready(function () {

    var rx = [];
    var tx = [];
    function ajaxFunction() {
        $.ajax({
            url: "mikrotik/monitorTraficReturn.php",
            method: "GET",
            dataType: "json",

            success: function (result) {
                $('.preDataMikrotik').hide();
                rx.unshift(result.rx_per_second);
                tx.unshift(result.tx_per_second);

                var dataConfig = {
                    type: 'line',
                    data: {
                        labels: ["", "", "", "", "", "", "", "", "", ""],
                        datasets: [{
                                label: "Recieved",
                                fill: false,
                                lineTension: 0.1,
                                backgroundColor: "rgba(229,0,43,0.4)",
                                borderColor: "rgba(229,0,43,1)",
                                borderCapStyle: 'butt',
                                borderDash: [],
                                borderDashOffset: 0.0,
                                borderJoinStyle: 'miter',
                                pointBorderColor: "rgba(231,0,43,1)",
                                pointBackgroundColor: "#fff",
                                pointBorderWidth: 1,
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: "rgba(230,0,43,1)",
                                pointHoverBorderColor: "rgba(235,0,43,1)",
                                pointHoverBorderWidth: 2,
                                pointRadius: 1,
                                pointHitRadius: 10,
                                data: rx,
                                spanGaps: false,
                            }, {
                                label: "Transmitted",
                                fill: false,
                                lineTension: 0.1,
                                backgroundColor: "rgba(55,192,192,0.4)",
                                borderColor: "rgba(33,192,192,1)",
                                borderCapStyle: 'butt',
                                borderDash: [],
                                borderDashOffset: 0.0,
                                borderJoinStyle: 'miter',
                                pointBorderColor: "rgba(11,192,192,1)",
                                pointBackgroundColor: "#fff",
                                pointBorderWidth: 1,
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: "rgba(55,192,192,1)",
                                pointHoverBorderColor: "rgba(66,220,220,1)",
                                pointHoverBorderWidth: 2,
                                pointRadius: 1,
                                pointHitRadius: 10,
                                data: tx,
                                spanGaps: false,
                            }]
                    },
                    options: {

                        animation: false,
                        responsive: true,
                        title: {
                            display: true,
                            text: 'Transmission Graph for LAN'
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {
                            xAxes: [{
                                    display: true,
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Time'
                                    }
                                }],
                            yAxes: [{
                                    display: true,
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Speed in kbps'
                                    }
                                }]
                        }
                    }
                };

                setInterval(function () {
                    dataConfig
                }, 1000);

                var mikrotikTrafiqChartx = document.getElementById("microtikTraficGraph").getContext("2d");

                var mikrotikChart = new Chart(mikrotikTrafiqChartx, dataConfig);
            } // succes function data
        }); // Ajax
    }
    //    ajaxFunction();



    setInterval(function () {
        ajaxFunction()
    }, 2000);


}); // document ready function