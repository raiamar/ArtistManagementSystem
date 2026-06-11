<?php
require_once __DIR__ . '/../../../src/models/user.php';
$page = max(1, (int)($_GET['p']  ?? 1));
$users = hasRole('super_admin') ? UserHandler::list($page) : null;
$sn = ($users['current_page'] - 1) * $users['per_page'];
?>

<header class="border-b border-solid border-gray-300 bg-white">
    <div class="flex justify-between items-center mb-4">
        <h2 class="p-6">User Management</h2>

        <button class="bg-blue-600 text-white px-4 py-2 mr-4 rounded-md hover:bg-blue-700 transition text-sm font-medium">+ New User</button>
    </div>
</header>
<section class="m-4 bg-white border border-gray-300 border-solid rounded shadow">



    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs uppercase bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-6 py-3">S.N</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Gender</th>
                    <th class="px-6 py-3">Phone</th>
                    <th class="px-6 py-3">DOB</th>
                    <th class="px-6 py-3">Address</th>
                    <th class="px-6 py-3">Role</th>
                    <th class="px-6 py-3">Action</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($users['data'] as $user): ?>
                    <?php $sn++; ?>
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4"><?= $sn ?></td>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            <?= $user['first_name'].' '.$user['last_name'] ?>
                        </td>
                        <td class="px-6 py-4"><?= $user['email'] ?></td>
                        <td class="px-6 py-4"><?= match($user['gender']){
                            'm'=>'Male',
                            'f'=>'Female',
                            'o'=>'Other',
                            default=>'Unknown'
                        } ?></td>
                        <td class="px-6 py-4"><?= $user['phone'] ?></td>
                        <td class="px-6 py-4"><?= date('d M Y', strtotime($user['dob'])) ?></td>
                        <td class="px-6 py-4"><?= $user['address'] ?></td>
                        <td class="px-6 py-4"><?= $user['role'] ?></td>
                        <td class="px-6 py-4">
                            <a href="#" class="text-orange-600 hover:underline">Edit</a>
                            <a href="#" class="text-red-600 ml-3 hover:underline">Delete</a>
                        </td>
                    </tr>
                <?php endforeach;  ?>
            </tbody>
        </table>
    </div>

    <div class="p-4">
        <?= paginationLinks($users, '?page=user') ?>
    </div>
</section>