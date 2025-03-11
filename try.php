<!-- BLANK PAGE FOR EDITING ONLY -->

<div class="w-full bg-white rounded-2xl flex flex-col max-h-full min-h-[80vh] ">
                                <!-- Profile Section (Fixed) -->
                                <div class="relative w-full shadow-md rounded-2xl flex-none overflow-hidden">
                                    <table class="w-full text-sm text-left text-gray-700 border-collapse">
                                        <thead class="text-gray-900 bg-gray-200 rounded-t-2xl">
                                            <tr>
                                                <th colspan="2"
                                                    class="px-6 py-4 text-left text-lg font-semibold bg-gray-200 rounded-t-2xl">
                                                    <div class="flex items-center justify-between">
                                                        <h3 class="text-lg font-semibold text-gray-800">Your Profile
                                                        </h3>
                                                        <button onclick="openModal()">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor"
                                                                class="w-6 h-6 text-blue-500 cursor-pointer hover:text-blue-700 transition">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-300 bg-white rounded-b-2xl overflow-hidden">
                                            <tr class="hover:bg-gray-100 transition">
                                                <td class="px-6 py-3 w-1/5 font-semibold">Name:</td>
                                                <td class="px-6 py-3"><?php echo htmlspecialchars($user['first_name']); ?>
                                                </td>
                                            </tr>
                                            <tr class="bg-gray-50 hover:bg-gray-100 transition">
                                                <td class="px-6 py-3 w-1/5 font-semibold">Age:</td>
                                                <td class="px-6 py-3"><?php echo htmlspecialchars($user['age']); ?></td>
                                            </tr>
                                            <tr class="hover:bg-gray-100 transition">
                                                <td class="px-6 py-3 w-1/5 font-semibold">Height:</td>
                                                <td class="px-6 py-3"><?php echo htmlspecialchars($user['height']); ?>
                                                    cm</td>
                                            </tr>
                                            <tr class="bg-gray-50 hover:bg-gray-100 transition rounded-b-2xl">
                                                <td class="px-6 py-3 w-1/5 font-semibold">Weight:</td>
                                                <td class="px-6 py-3"><?php echo htmlspecialchars($user['weight']); ?>
                                                    kg</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>