<!DOCTYPE html>
<html lang="en">

<head>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BiteTrack - Your Goals!</title>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script defer src="script.js"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="custom/css/custom.css">
        <link rel="stylesheet" href="css/button.css">
        <link rel="stylesheet" href="css/sidebar.css">
    </head>

<body class="flex flex-col min-h-screen">
    <!--MAIN CONTENT START-->
    <main class="flex-grow">
        <!-- ===== Page Wrapper Start ===== -->
        <div class="flex h-screen overflow-hidden">
            <?php require_once 'includes/sidebar.php'; ?>
            <!-- ===== Content Area Start ===== -->
            <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-hidden">
                <!-- Small Device Overlay Start -->
                <div :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
                    class="fixed z-9 h-screen w-full bg-gray-900/50 hidden"></div>
                <!-- Small Device Overlay End -->

                <!-- ===== Main Content Start ===== -->
                <main class="p-6 mx-auto max-w-full min-h-screen">
                    <!-- Main Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 h-full w-screen max-w-full flex-1">
                        <!-- Profile Section -->
                        <div class="w-full bg-white rounded-2xl flex flex-col max-h-full min-h-[80vh] ">
                            <!-- Profile Section (Fixed) -->
                            <div class="relative w-full shadow-md rounded-2xl flex-none overflow-hidden">
                                <table class="w-full text-sm text-left text-gray-700 border-collapse">
                                    <thead class="text-gray-900 bg-gray-200 rounded-t-2xl">
                                        <tr>
                                            <th colspan="2" class="px-6 py-4 text-left text-lg font-semibold bg-gray-200 rounded-t-2xl">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-lg font-semibold text-gray-800">Your Profile</h3>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-6 h-6 text-blue-500 cursor-pointer hover:text-blue-700 transition">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                    </svg>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-300 bg-white rounded-b-2xl overflow-hidden">
                                        <tr class="hover:bg-gray-100 transition">
                                            <td class="px-6 py-3 w-1/5 font-semibold">Name:</td>
                                            <td class="px-6 py-3">Desiree Soronio</td>
                                        </tr>
                                        <tr class="bg-gray-50 hover:bg-gray-100 transition">
                                            <td class="px-6 py-3 w-1/5 font-semibold">Age:</td>
                                            <td class="px-6 py-3">20</td>
                                        </tr>
                                        <tr class="hover:bg-gray-100 transition">
                                            <td class="px-6 py-3 w-1/5 font-semibold">Height:</td>
                                            <td class="px-6 py-3">164 cm</td>
                                        </tr>
                                        <tr class="bg-gray-50 hover:bg-gray-100 transition rounded-b-2xl">
                                            <td class="px-6 py-3 w-1/5 font-semibold">Weight:</td>
                                            <td class="px-6 py-3">62 kg</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


                            <!-- Goals Section (Scrollable) -->
                            <div class="mt-4 shadow-lg shadow-gray-500/50  overflow-hidden rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-md flex-grow overflow-y-auto max-h-full">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-lg font-semibold text-gray-800">Goals</h3>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="w-6 h-6 text-blue-500 cursor-pointer hover:text-blue-700 transition">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </div>

                                <!-- Goals List -->
                                <div class="space-y-4">
                                    <div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">
                                        <h5 class="text-md font-semibold text-gray-900">Lower My Calorie Intake</h5>
                                        <p class="text-sm text-gray-700">Reduce your overall calorie intake to promote weight loss while maintaining essential nutrients.</p>
                                    </div>
                                    <div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">
                                        <h5 class="text-md font-semibold text-gray-900">Eat More Protein</h5>
                                        <p class="text-sm text-gray-700">Increase your protein intake to support muscle growth and recovery.</p>
                                    </div>
                                    <div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">
                                        <h5 class="text-md font-semibold text-gray-900">Drink More Water</h5>
                                        <p class="text-sm text-gray-700">Stay hydrated to improve focus, digestion, and overall well-being.</p>
                                    </div>
                                    <div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">
                                        <h5 class="text-md font-semibold text-gray-900">Reduce Unhealthy Fats</h5>
                                        <p class="text-sm text-gray-700">Limit your intake of saturated and trans fats to support heart health and overall well-being.</p>
                                    </div>
                                    <div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">
                                        <h5 class="text-md font-semibold text-gray-900">Less Sodium</h5>
                                        <p class="text-sm text-gray-700">Reduce your sodium intake to support heart health and maintain healthy blood pressure levels.</p>
                                    </div>
                                    <div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">
                                        <h5 class="text-md font-semibold text-gray-900">Eat More Fiber</h5>
                                        <p class="text-sm text-gray-700">Boost digestion and gut health by including fiber-rich foods in your diet.</p>
                                    </div>
                                    <div class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition p-4">
                                        <h5 class="text-md font-semibold text-gray-900">Avoid Processed Foods</h5>
                                        <p class="text-sm text-gray-700">Choose whole, natural foods over processed options for better health.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Edit Goals Section -->
                        <div class="w-full bg-white rounded-2xl flex flex-col items-center justify-start h-full min-h-[80vh] ">
                            <div class="relative w-full shadow-lg shadow-gray-500/50 rounded-2xl flex-none overflow-hidden self-start">
                                <table class="w-full text-sm text-left text-gray-700 border-collapse">
                                    <thead class="text-gray-900 rounded-t-lg">
                                        <tr>
                                            <th colspan="3" class="px-4 py-3 text-left text-lg font-semibold rounded-t-lg ">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-lg font-semibold text-gray-800">Your Progress</h3>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-300 bg-white rounded-b-lg">
                                        <tr class="hover:bg-gray-100 transition">
                                            <td class="px-4 py-2 w-2/5 font-semibold">Calorie Intake</td>
                                            <td class="px-4 py-2">
                                                <div class="w-full bg-gray-200 rounded-full">
                                                    <div class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-full"
                                                        style="background: linear-gradient(to right, #FCD404, #FB6F74); width: 90%">
                                                        90%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="bg-gray-50 hover:bg-gray-100 transition">
                                            <td class="px-4 py-2 font-semibold">Protein Consumption</td>
                                            <td class="px-4 py-2">
                                                <div class="w-full bg-gray-200 rounded-full">
                                                    <div class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-full"
                                                        style="background: linear-gradient(to right, #FCD404, #FB6F74); width: 70%">
                                                        70%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-100 transition">
                                            <td class="px-4 py-2 font-semibold">Hydration Level</td>
                                            <td class="px-4 py-2">
                                                <div class="w-full bg-gray-200 rounded-full">
                                                    <div class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-full"
                                                        style="background: linear-gradient(to right, #FCD404, #FB6F74); width: 50%">
                                                        50%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="bg-gray-50 hover:bg-gray-100 transition rounded-b-lg">
                                            <td class="px-4 py-2 font-semibold">Weight Loss</td>

                                            <td class="px-4 py-2">
                                                <div class="w-full bg-gray-200 rounded-full">
                                                    <div class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-full"
                                                        style="background: linear-gradient(to right, #FCD404, #FB6F74); width: 60%">
                                                        60%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="bg-gray-50 hover:bg-gray-100 transition rounded-b-lg">
                                            <td class="px-4 py-2 font-semibold">Fat Intake</td>

                                            <td class="px-4 py-2">
                                                <div class="w-full bg-gray-200 rounded-full">
                                                    <div class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-full"
                                                        style="background: linear-gradient(to right, #FCD404, #FB6F74); width: 10%">
                                                        10%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="bg-gray-50 hover:bg-gray-100 transition rounded-b-lg">
                                            <td class="px-4 py-2 font-semibold">Sodium Intake</td>

                                            <td class="px-4 py-2">
                                                <div class="w-full bg-gray-200 rounded-full">
                                                    <div class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-full"
                                                        style="background: linear-gradient(to right, #FCD404, #FB6F74); width: 29%">
                                                        29%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="bg-gray-50 hover:bg-gray-100 transition rounded-b-lg">
                                            <td class="px-4 py-2 font-semibold">Balanced Diet Score</td>

                                            <td class="px-4 py-2">
                                                <div class="w-full bg-gray-200 rounded-full">
                                                    <div class="text-xs font-medium text-white text-center p-0.5 leading-none rounded-full"
                                                        style="background: linear-gradient(to right, #FCD404, #FB6F74); width: 89%">
                                                        89%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--Progress Track End-->
                            <!--Streak-->
                            <div class="mt-2 pl-4 relative flex flex-col rounded-2xl bg-white shadow-lg shadow-gray-500/50 w-full max-h-full">
                                <div class="flex-grow">
                                    <h3 class="text-xl font-semibold text-gray-800 mt-2">Keep the Streak Alive!</h3>
                                    <p class="text-gray-600">Your dedication, visualized</p>
                                </div>
                                <div id="line-chart" class="w-full h-full"></div>
                            </div>



                            <script>
                                const chartConfig = {
                                    series: [{
                                        name: "Sales",
                                        data: [50, 40, 300, 320, 500, 350, 200, 230, 500],
                                    }, ],
                                    chart: {
                                        type: "line",
                                        height: 240,
                                        toolbar: {
                                            show: false,
                                        },
                                    },
                                    title: {
                                        show: "",
                                    },
                                    dataLabels: {
                                        enabled: false,
                                    },
                                    colors: ["#FB6F74", "#FCD404"],
                                    stroke: {
                                        lineCap: "round",
                                        curve: "smooth",
                                    },
                                    markers: {
                                        size: 0,
                                    },
                                    xaxis: {
                                        axisTicks: {
                                            show: false,
                                        },
                                        axisBorder: {
                                            show: false,
                                        },
                                        labels: {
                                            style: {
                                                colors: "#616161",
                                                fontSize: "12px",
                                                fontFamily: "inherit",
                                                fontWeight: 400,
                                            },
                                        },
                                        categories: [
                                            "Apr",
                                            "May",
                                            "Jun",
                                            "Jul",
                                            "Aug",
                                            "Sep",
                                            "Oct",
                                            "Nov",
                                            "Dec",
                                        ],
                                    },
                                    yaxis: {
                                        labels: {
                                            style: {
                                                colors: "#616161",
                                                fontSize: "12px",
                                                fontFamily: "inherit",
                                                fontWeight: 400,
                                            },
                                        },
                                    },
                                    grid: {
                                        show: true,
                                        borderColor: "#dddddd",
                                        strokeDashArray: 5,
                                        xaxis: {
                                            lines: {
                                                show: true,
                                            },
                                        },
                                        padding: {
                                            top: 5,
                                            right: 20,
                                        },
                                    },
                                    fill: {
                                        opacity: 0.8,
                                    },
                                    tooltip: {
                                        theme: "dark",
                                    },
                                };

                                const chart = new ApexCharts(document.querySelector("#line-chart"), chartConfig);

                                chart.render();
                            </script>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </main>

    <!-- ===== Main Content End ===== -->
    </div>
    <!-- ===== Content Area End ===== -->
    </div>
    <!-- ===== Page Wrapper End ===== -->
    </main>
</body>

<script defer="" src="bundle.js"></script>
<script defer=""
    src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015"
    integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ=="
    data-cf-beacon="{&quot;rayId&quot;:&quot;91b7c147fdd902a9&quot;,&quot;version&quot;:&quot;2025.1.0&quot;,&quot;r&quot;:1,&quot;token&quot;:&quot;67f7a278e3374824ae6dd92295d38f77&quot;,&quot;serverTiming&quot;:{&quot;name&quot;:{&quot;cfExtPri&quot;:true,&quot;cfL4&quot;:true,&quot;cfSpeedBrain&quot;:true,&quot;cfCacheStatus&quot;:true}}}"
    crossorigin="anonymous"></script>


<svg id="SvgjsSvg1001" width="2" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1"
    xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev"
    style="overflow: hidden; top: -100%; left: -100%; position: absolute; opacity: 0;">
    <defs id="SvgjsDefs1002"></defs>
    <polyline id="SvgjsPolyline1003" points="0,0"></polyline>
    <path id="SvgjsPath1004" d="M0 0 ">

    </path>
</svg>

<div class="jvm-tooltip"></div>

</body>

</html>