<?php

if (!isLoggedIn())
    redirect(APP_URL . 'login.php');

$pageTitle = 'Dashboard -' . APP_NAME;

$successMessage = $_SESSION['login_success_message'] ?? null;
if ($successMessage)
    unset($_SESSION['login_success_message']);

$user = currenctUser();
?>

<header class="border-b border-solid border-gray-300 bg-white">
    <div class="inline-flex">
        <h2 class="p-6">Dashboard</h2>

        <?php if ($successMessage): ?>
            <div id="alert-3" class="flex sm:items-center p-4 mb-4 text-sm text-fg-success-strong rounded-base bg-green-200" role="alert">
                <svg class="w-4 h-4 shrink-0 mt-0.5 md:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span class="sr-only">Info</span>
                <div class="ms-2 text-sm ">
                    <?= htmlspecialchars($successMessage . $user['name']) ?>
                </div>
                <button type="button" onclick="document.getElementById('alert-3').remove();" class="ml-auto -mx-1.5 -my-1.5 p-1.5 rounded-lg hover:bg-green-200 focus:ring-2 focus:ring-green-300 inline-flex h-8 w-8" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                </button>
            </div>
        <?php endif; ?>
    </div>
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
    