@section('header', 'Customer Management')

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex-1 max-w-md">
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search customers..."
                    class="w-full pl-10 pr-4 py-2.5 border border-zinc-200 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all"
                >
                <svg class="absolute left-3 top-3 w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
        <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg transition-colors duration-150 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="create"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24" wire:loading wire:target="create">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="create">Add Customer</span>
            <span wire:loading wire:target="create">Loading...</span>
        </button>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50/50">
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Card Number</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Customer</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Contact</th>
                    <th class="px-6 py-3.5 text-center text-sm font-medium text-zinc-700">Tier</th>
                    <th class="px-6 py-3.5 text-center text-sm font-medium text-zinc-700">Points</th>
                    <th class="px-6 py-3.5 text-right text-sm font-medium text-zinc-700">Total Spent</th>
                    <th class="px-6 py-3.5 text-center text-sm font-medium text-zinc-700">Status</th>
                    <th class="px-6 py-3.5 text-center text-sm font-medium text-zinc-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($customers as $customer)
                    <tr wire:key="customer-{{ $customer->id }}" class="hover:bg-zinc-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-mono text-zinc-600">{{ $customer->card_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-zinc-900">{{ $customer->name }}</span>
                                <span class="text-xs text-zinc-500">Member since {{ $customer->member_since->format('M Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-zinc-600">{{ $customer->phone }}</span>
                                @if($customer->email)
                                    <span class="text-xs text-zinc-500">{{ $customer->email }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm">{{ $customer->tier_badge }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                {{ number_format($customer->total_points) }} pts
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-medium text-zinc-900">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($customer->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Active</span>
                            @elseif($customer->status === 'inactive')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700">Inactive</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Blocked</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="viewCard({{ $customer->id }})" class="text-indigo-600 hover:text-indigo-700 p-1.5 hover:bg-indigo-50 rounded transition-colors" title="View Card">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                </button>
                                <button wire:click="openPointModal({{ $customer->id }})" class="text-emerald-600 hover:text-emerald-700 p-1.5 hover:bg-emerald-50 rounded transition-colors" title="Adjust Points">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <button wire:click="edit({{ $customer->id }})" class="text-blue-600 hover:text-blue-700 p-1.5 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button onclick="confirmDelete({{ $customer->id }}, '{{ $customer->name }}')" class="text-red-600 hover:text-red-700 p-1.5 hover:bg-red-50 rounded transition-colors" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-zinc-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            No customers found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        {{ $customers->links() }}
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Header -->
                <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between flex-shrink-0">
                    <h3 class="text-lg font-semibold text-white">{{ $customerId ? 'Edit Customer' : 'Add New Customer' }}</h3>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Name *</label>
                            <input type="text" wire:model="name" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Phone *</label>
                            <input type="text" wire:model="phone" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Email</label>
                            <input type="email" wire:model="email" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Date of Birth</label>
                            <input type="date" wire:model="date_of_birth" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('date_of_birth') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Gender</label>
                            <select wire:model="gender" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            @error('gender') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Address</label>
                            <textarea wire:model="address" rows="2" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Status</label>
                            <select wire:model="status" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="blocked">Blocked</option>
                            </select>
                            @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Notes</label>
                            <textarea wire:model="notes" rows="2" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-zinc-50 px-6 py-4 border-t border-zinc-200 flex justify-end gap-3">
                    <button wire:click="closeModal" class="bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-700 font-medium px-4 py-2 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button wire:click="save" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                        {{ $customerId ? 'Update' : 'Create' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Point Adjustment Modal -->
    @if($showPointModal && $selectedCustomer)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
                <div class="bg-emerald-600 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Adjust Points</h3>
                    <button wire:click="closePointModal" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-sm text-zinc-600">Customer: <span class="font-medium text-zinc-900">{{ $selectedCustomer->name }}</span></p>
                        <p class="text-sm text-zinc-600">Current Points: <span class="font-medium text-indigo-600">{{ number_format($selectedCustomer->total_points) }}</span></p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Points Amount *</label>
                            <input type="number" wire:model="pointAmount" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Positive to add, negative to deduct">
                            @error('pointAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            <p class="text-xs text-zinc-500 mt-1">Enter positive number to add points, negative to deduct</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Description *</label>
                            <textarea wire:model="pointDescription" rows="2" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Reason for adjustment"></textarea>
                            @error('pointDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-50 px-6 py-4 border-t border-zinc-200 flex justify-end gap-3">
                    <button wire:click="closePointModal" class="bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-700 font-medium px-4 py-2 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button wire:click="adjustPoints" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                        Adjust Points
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Member Card Modal -->
    @if($showCardModal && $selectedCustomer)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Member Card - {{ $selectedCustomer->name }}</h3>
                    <div class="flex items-center gap-2">
                        <button onclick="window.print()" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print Card
                        </button>
                        <button wire:click="closeCardModal" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="p-8">
                    <!-- Member Card Preview -->
                    <div id="member-card" class="mx-auto" style="width: 513.6px; height: 324px; transform-origin: top center;">
                        <!-- Card Front -->
                        <div class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-2xl shadow-2xl overflow-hidden" style="width: 513.6px; height: 324px;">
                            <!-- Background Pattern -->
                            <div class="absolute inset-0 opacity-10">
                                <div class="absolute top-0 right-0 w-56 h-56 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                                <div class="absolute bottom-0 left-0 w-40 h-40 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
                            </div>

                            <!-- Card Content -->
                            <div class="relative h-full p-6 flex flex-col justify-between">
                                <!-- Header -->
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h2 class="text-white text-xl font-bold mb-1">{{ \App\Models\Setting::getValue('business_name', config('app.name', 'POS System')) }}</h2>
                                        <p class="text-white/80 text-xs">Member Card</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="inline-block px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-lg">
                                            <p class="text-white text-base font-bold">{{ $selectedCustomer->tier_badge }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Middle Section -->
                                <div class="flex items-end justify-between">
                                    <!-- Customer Info -->
                                    <div class="flex-1">
                                        <div class="mb-4">
                                            <p class="text-white/60 text-xs mb-1">Card Number</p>
                                            <p class="text-white text-base font-mono font-bold tracking-wider">{{ $selectedCustomer->card_number }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <p class="text-white/60 text-xs mb-1">Member Name</p>
                                            <p class="text-white text-sm font-semibold">{{ $selectedCustomer->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-white/60 text-xs mb-1">Member Since</p>
                                            <p class="text-white text-xs font-medium">{{ $selectedCustomer->member_since->format('M Y') }}</p>
                                        </div>
                                    </div>

                                    <!-- QR Code -->
                                    <div class="bg-white p-2.5 rounded-lg shadow-lg">
                                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(84)->generate($selectedCustomer->card_number) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Info -->
                    <div class="mt-6 bg-zinc-50 rounded-lg p-4">
                        <h4 class="font-semibold text-zinc-900 mb-3">Member Benefits:</h4>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-zinc-600">🥈 <strong>Silver:</strong> 1x points</p>
                            </div>
                            <div>
                                <p class="text-zinc-600">🥇 <strong>Gold:</strong> 1.5x points</p>
                            </div>
                            <div>
                                <p class="text-zinc-600">💎 <strong>Platinum:</strong> 2x points</p>
                            </div>
                        </div>
                        <p class="text-xs text-zinc-500 mt-3">Earn 1 point per Rp 10,000 • Redeem 100 points = Rp 10,000 discount</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Styles -->
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }
                #member-card, #member-card * {
                    visibility: visible;
                }
                #member-card {
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                }
                @page {
                    size: 856px 540px;
                    margin: 0;
                }
                }\n            }\n        </style>
    @endif
</div>

<script>
    // Toast notification listener
    document.addEventListener('livewire:init', () => {
        Livewire.on('success', (event) => {
            const message = event.message || event[0]?.message || 'Operation successful!';
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        });
    });

    // SweetAlert2 delete confirmation
    function confirmDelete(customerId, customerName) {
        Swal.fire({
            title: 'Delete Customer?',
            text: `Are you sure you want to delete "${customerName}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('delete', customerId);
            }
        });
    }
</script>
