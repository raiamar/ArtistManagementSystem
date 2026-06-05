<?php
    $pageTitle = 'Dashboard -' . APP_NAME;
?>

<header class="border-b border-solid border-gray-300 bg-white">
    <h2 class="p-6">Dashboard</h2>
</header>
<section class="m-4 bg-white border border-gray-300 border-solid rounded shadow">
    <header class="border-b border-solid border-gray-300 p-4 text-lg font-medium">
        Overview
    </header>
    <section class=" flex flex-row flex-wrap items-center text-center border-b border-solid border-gray-300">
        <div class="p-4 w-full sm:w-1/2 lg:w-1/4 border-b border-solid border-gray-300 md:border-b-0 sm:border-r">
            <span class="text-xs font-medium text-gray-500 uppercase">Total Artist</span>
            <div class="py-4 flex items-center justify-center text-center">
                <span class="mr-4 text-3xl">0</span>
            </div>
        </div>
        <div class="p-4 w-full sm:w-1/2 lg:w-1/4 border-b border-solid border-gray-300 md:border-b-0 sm:border-r">
            <span class="text-xs font-medium text-gray-500 uppercase">Total Music</span>
            <div class="py-4 flex items-center justify-center text-center">
                <span class="mr-4 text-3xl">0</span>
            </div>
        </div>
        <div class="p-4 w-full sm:w-1/2 lg:w-1/4 border-b border-solid border-gray-300 md:border-b-0 sm:border-r">
            <span class="text-xs font-medium text-gray-500 uppercase">Total Manager</span>
            <div class="py-4 flex items-center justify-center text-center">
                <span class="mr-4 text-3xl">0</span>
            </div>
        </div>

    </section>
    <section id="chart" class="p-4">
        <canvas id="myChart" width="200" height="200"></canvas>
    </section>