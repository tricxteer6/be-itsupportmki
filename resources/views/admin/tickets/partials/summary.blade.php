<div class="rounded-xl border border-slate-200 bg-white p-4">
    <p class="text-[11px] uppercase tracking-wide text-slate-500"><i class="ri-time-line mr-1"></i>Pending</p>
    <p class="mt-1 text-2xl font-bold text-slate-800">{{ $summary['pending'] ?? 0 }}</p>
</div>
<div class="rounded-xl border border-slate-200 bg-white p-4">
    <p class="text-[11px] uppercase tracking-wide text-slate-500"><i class="ri-check-line mr-1"></i>Accepted</p>
    <p class="mt-1 text-2xl font-bold text-slate-800">{{ $summary['accepted'] ?? 0 }}</p>
</div>
<div class="rounded-xl border border-slate-200 bg-white p-4">
    <p class="text-[11px] uppercase tracking-wide text-slate-500"><i class="ri-loader-4-line mr-1"></i>In Progress</p>
    <p class="mt-1 text-2xl font-bold text-slate-800">{{ $summary['in_progress'] ?? 0 }}</p>
</div>
<div class="rounded-xl border border-slate-200 bg-white p-4">
    <p class="text-[11px] uppercase tracking-wide text-slate-500"><i class="ri-flag-line mr-1"></i>Finished</p>
    <p class="mt-1 text-2xl font-bold text-slate-800">{{ $summary['finished'] ?? 0 }}</p>
</div>
