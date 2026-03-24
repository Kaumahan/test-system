<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900">

    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight">Transactions</h1>
                <p class="text-slate-500 text-sm">Manage normalized transaction data from CSV imports.</p>
            </div>
            
            <form action="{{ route('transactions.upload') }}" method="POST" enctype="multipart/form-data" 
                  class="bg-white p-3 rounded-xl shadow-sm border border-slate-200 flex items-center gap-3">
                @csrf
                <input type="file" name="csv_file" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap">
                    IMPORT
                </button>
            </form>
        </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-700 rounded-lg text-sm font-medium">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-amber-50 border border-amber-100 text-amber-700 rounded-lg text-sm font-medium">
        <strong>Warning:</strong> {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-lg text-sm font-medium">
        <strong>Success!</strong> {{ session('success') }}
    </div>
    @endif

    @if(session('info'))
    <div class="mb-6 p-4 bg-blue-50 border border-blue-100 text-blue-700 rounded-lg text-sm font-medium">
        <strong>Notice:</strong> {{ session('info') }}
    </div>
    @endif


        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 mb-6">
            <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Business</label>
                    <select name="business_id" class="w-full border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none p-2 bg-slate-50">
                        <option value="">All Businesses</option>
                        @foreach($businesses as $biz)
                            <option value="{{ $biz->id }}" {{ request('business_id') == $biz->id ? 'selected' : '' }}>{{ $biz->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</label>
                    <select name="status" class="w-full border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none p-2 bg-slate-50">
                        <option value="">All Statuses</option>
                        <option value="Reviewed" {{ request('status') == 'Reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <div class="lg:col-span-2 flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-black transition">Apply Filters</button>
                    <a href="{{ route('transactions.index') }}" class="px-4 py-2 border border-slate-200 rounded-lg text-sm font-bold text-slate-500 hover:bg-slate-50 transition">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="p-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Date</th>
                            <th class="p-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Details</th>
                            <th class="p-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Amount</th>
                            <th class="p-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Business</th>
                            <th class="p-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($transactions as $t)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="p-4 text-sm text-slate-500 tabular-nums">
                                {{ $t->date }}
                            </td>
                            <td class="p-4">
                                <div class="text-sm font-bold text-slate-800">{{ $t->description }}</div>
                                <div class="inline-block mt-1 px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded text-[10px] font-medium">
                                    {{ $t->category->name }}
                                </div>
                            </td>
                            <td class="p-4 text-sm font-bold text-right tabular-nums {{ $t->amount < 0 ? 'text-rose-500' : 'text-emerald-500' }}">
                                {{ number_format($t->amount, 2) }}
                            </td>
                            <td class="p-4 text-sm text-slate-600 font-medium">
                                {{ $t->business->name }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter {{ $t->status == 'Reviewed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $t->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-20 text-center text-slate-400 italic text-sm">No transactions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8">
            {{ $transactions->links() }}
        </div>
    </div>

</body>
</html>