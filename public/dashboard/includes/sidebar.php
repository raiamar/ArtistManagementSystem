 <?php 
    require_once __DIR__.'/../../../src/models/auth.php';
    $user = currenctUser();
 ?>
        <aside class="w-full md:h-screen md:w-64 bg-gray-900 md:flex md:flex-col">
            <header class="border-b border-solid border-gray-800 flex-grow">
                <h1 class="py-6 px-4 text-gray-100 text-base font-medium">Artist Management System</h1>
            </header>
            <nav class="overflow-y-auto h-full flex-grow">
                <ul class="font-medium px-4 text-left">
                    <li class="text-gray-100">
                        <a href="?page=dashboard" class="rounded text-sm text-left block py-3 px-6 hover:bg-blue-600 w-full">Dashboard</a>
                        <a href="?page=artist" class="rounded text-sm block py-3 px-6 hover:bg-blue-600 w-full text-left">Artist</a>
                    </li>
                </ul>
            </nav>


            <footer class="p-4 border-t border-gray-800">
                <div class="flex flex-col mb-3">
                    <span class="text-white"><?= $user['name'] ?></span>
                    <span class="text-xs text-gray-500"><?= $user['role'] ?></span>
                </div>

                <a
                    href="/../record-manager/public/logout.php"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium bg-white text-black border rounded hover:bg-gray-100">
                    <span class="mr-2">Logout</span>
                    <i class="fa-solid fa-right-to-bracket"></i>
                </a>
            </footer>
        </aside>