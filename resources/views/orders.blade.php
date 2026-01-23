<div class="flex flex-col h-screen bg-third-light shadow-xl border-l border-gray-200 w-full max-w-[340px] ml-auto">

    <div class="p-3 flex items-center justify-between border-b border-gray-100 gap-2">
        <button
            class="flex items-center gap-1.5 bg-secondary px-2.5 py-2 rounded-lg text-xs font-semibold text-gray-200 hover:bg-primary whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Customer
        </button>

        <div class="flex gap-1.5">
            <button class="p-2 bg-secondary rounded-lg text-gray-100 hover:bg-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" />
                </svg>
            </button>
            <button class="p-2 bg-secondary rounded-lg text-gray-100 hover:bg-primary">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 4a1 1 0 00-2 0v12a1 1 0 002 0V4zM9 4a1 1 0 00-2 0v12a1 1 0 002 0V4z" />
                </svg>
            </button>
            <button class="p-2 bg-secondary rounded-lg text-gray-100 hover:bg-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-3 space-y-2">
        <x-order-item-card qty="1" name="Thai Style Fried Noodles" price="40.00" originalPrice="50.00"
            description="Medium" discount="20" expanded="true" />

        <x-order-item-card qty="1" name="Schezwan Egg Noodles" price="25.00" />
    </div>

    <div class="bg-secondary p-3 border-t border-gray-200">
        <div class="bg-third-light rounded-lg p-2.5 flex justify-between items-center mb-4">
            <span class="text-xs font-bold text-gray-700">Add</span>
            <div class="flex gap-3 text-[10px] font-bold text-secondary uppercase">
                <button>Disc</button>
                <button>Coupon</button>
                <button>Note</button>
            </div>
        </div>

        <div class="space-y-1.5 mb-4">
            <div class="flex justify-between text-[11px] text-gray-100/80 font-medium uppercase tracking-wider">
                <span>Subtotal</span>
                <span class="font-bold">$200.00</span>
            </div>
            <div class="flex justify-between text-[11px] text-gray-100/80 font-medium uppercase tracking-wider">
                <span>Tax</span>
                <span class="font-bold">$45.00</span>
            </div>
            <div class="flex justify-between pt-1.5 border-t border-white/10 mt-1">
                <span class="text-xs font-black text-white uppercase italic">Total</span>
                <span class="text-sm font-black text-white">$195.00</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <button
                class="flex flex-col items-center justify-center bg-[#FF8A1E] text-white py-2.5 rounded-lg font-bold hover:brightness-95 transition text-[10px] uppercase">
                <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Hold
            </button>
            <button
                class="flex flex-col items-center justify-center bg-[#00B929] text-white py-2.5 rounded-lg font-bold hover:brightness-95 transition text-[10px] uppercase">
                <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Proceed
            </button>
        </div>
    </div>
</div>
