<?php

require_once __DIR__ . '/../config/config.php';

$pageTitle = 'Register -' . APP_NAME;
require_once __DIR__ . '/../layout/header.php'
?>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 p-8">
        <h4 class="text-xl font-bold text-center text-gray-800 mb-8"><?= APP_NAME ?></h4>

        <form class="w-full">
            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <label for="fname" class="mb-2 text-slate-900 font-medium text-sm inline-block">First
                        Name</label>
                    <input type="text" id="fname" name="fname" placeholder="Amar" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                </div>
                <div>
                    <label for="lname" class="mb-2 text-slate-900 font-medium text-sm inline-block">Last
                        Name</label>
                    <input type="text" id="lname" name="lname" placeholder="Rai" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                </div>
                <div>
                    <label for="email"
                        class="mb-2 text-slate-900 font-medium text-sm inline-block">Email</label>
                    <input type="email" id="email" name="email" placeholder="amar@gmail.com" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                </div>
                <div>
                    <label for="mobile"
                        class="mb-2 text-slate-900 font-medium text-sm inline-block">Mobile Number</label>
                    <input type="tel" id="mobile" name="mobile" placeholder="+977-9876543210" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                </div>


                <div>
                    <label for="dob" class="mb-2 text-slate-900 font-medium text-sm inline-block">Date of Birth</label>
                    <input type="date" id="dob" name="dob" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                </div>

                <div>
                    <label for="gender" class="mb-2 text-slate-900 font-medium text-sm inline-block">Gender</label>
                    <select id="gender" name="gender" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600">
                        <option value="" disabled selected>Select gender</option>
                        <option value="m">Male</option>
                        <option value="f">Female</option>
                        <option value="o">Other</option>
                    </select>
                </div>

                 <div>
                    <label for="address" class="mb-2 text-slate-900 font-medium text-sm inline-block">Address</label>
                    <input type="text" id="address" name="address" placeholder="Lalitpur, Nepal" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                </div>

                <div>
                    <label for="role" class="mb-2 text-slate-900 font-medium text-sm inline-block">Register As</label>
                    <select id="role" name="role" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600">
                        <option value="" disabled selected>Select role</option>
                        <option value="super_admin">Admin</option>
                        <option value="artist_manager">Manager</option>
                        <option value="artist">Artist</option>
                    </select>
                </div>

                <div>
                    <label for="password"
                        class="mb-2 text-slate-900 font-medium text-sm inline-block">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                </div>
                <div>
                    <label for="cpassword"
                        class="mb-2 text-slate-900 font-medium text-sm inline-block">Confirm
                        Password</label>
                    <input type="password" id="cpassword" name="cpassword" placeholder="••••••••" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                </div>

            </div>




            <div class="flex items-center justify-between mt-4">

                <p class="text-sm text-gray-600">
                    I have an account? <a href="login.php" class="text-blue-600 hover:underline">Login</a>
                </p>

                <button type="submit"
                    class="py-2 px-3.5 text-sm rounded-md font-semibold cursor-pointer tracking-wide text-white border border-blue-600 bg-blue-600 hover:bg-blue-700 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                    Create an account</button>
            </div>
        </form>
    </div>
</div>

<?= require_once __DIR__ . '/../layout/footer.php' ?>