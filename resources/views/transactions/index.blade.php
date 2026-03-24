<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased">

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <header class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-800">Financial Transactions</h1>
                <p class="text-slate-500 text-sm">Upload and manage your CSV transaction records.</p>
            </div>
            
            <form action="{{ route('transactions.upload') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="file" name="csv_file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition" required>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
                    Import CSV
                </button>
            </form>
        </header>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6">
            <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-slate-500 uppercase">Business</label>
                    <select name="business" class="border border-slate-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                        <option value="">All Businesses</option>
                        @foreach($businesses as $biz)
                            <option value="{{ $biz }}" {{ request('business') == $biz ? 'selected' : '' }}>{{ $biz }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-slate-500 uppercase">Status</label>
                    <select name="status" class="border border-slate-200 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">
                        <option value="">All Statuses</option>
                        <option value="Reviewed" {{ request('status') == 'Reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-2">
                    <button type="submit" class="w-full sm:w-auto bg-slate-800 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-slate-900 transition">Apply Filters</button>
                    <a href="{{ route('transactions.index') }}" class="w-full sm:w-auto text-center border border-slate-200 px-6 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Clear</a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Description</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Amount</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Business</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $t)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4 text-sm text-slate-600 whitespace-nowrap">{{ $t->date }}</td>
                            <td class="p-4">
                                <div class="text-sm font-semibold text-slate-800">{{ $t->description }}</div>
                                <div class="text-xs text-slate-400">{{ $t->category }}</div>
                            </td>
                            <td class="p-4 text-sm font-mono text-right {{ $t->amount < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $t->amount < 0 ? '-' : '' }}${{ number_format(abs($t->amount), 2) }}
                            </td>
                            <td class="p-4 text-sm text-slate-600">{{ $t->business }}</td>
                            <td class="p-4 text-center">
                                <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $t->status == 'Reviewed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $t->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="box"></path></svg>
                                    <p class="text-slate-500 font-medium">No transactions found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    </div>

</body>
</html>