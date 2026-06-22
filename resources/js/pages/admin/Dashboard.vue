<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Users, FileCheck, CreditCard, Trophy, Upload } from 'lucide-vue-next';
import CitechDashboardLayout from '@/components/CitechDashboardLayout.vue';

const props = defineProps({
    statistics: Object,
});
</script>

<template>
    <Head title="Dashboard Admin - CITECH 2026" />

    <CitechDashboardLayout activeMenu="admin.dashboard" role="admin">
        <template #header-title>
            <h2 class="text-lg font-black tracking-wide text-slate-800">Beranda Admin</h2>
        </template>

        <div class="space-y-6 animate-fade-in-up">
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-slate-100">
                <h3 class="text-xl font-extrabold text-slate-800">Selamat Datang di Panel Admin CITECH</h3>
                <p class="text-slate-500 text-sm mt-2 leading-relaxed">
                    Gunakan sidebar di sebelah kiri untuk menavigasi menu pengelolaan berkas persyaratan, verifikasi pembayaran, data tim, karya submission, dan penjadwalan tanggal timeline lomba.
                </p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                            <Users class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Tim</p>
                            <p class="text-2xl font-black text-slate-800">{{ statistics.totalTim }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <Trophy class="w-5 h-5 text-emerald-600" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tim Terverifikasi</p>
                            <p class="text-2xl font-black text-slate-800">{{ statistics.totalTimVerified }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                            <Upload class="w-5 h-5 text-purple-600" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Submission</p>
                            <p class="text-2xl font-black text-slate-800">{{ statistics.totalSubmission }}</p>
                        </div>
                    </div>
                </div>

                <Link :href="route('admin.persyaratan')" class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 hover:shadow-md transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                            <FileCheck class="w-5 h-5 text-amber-600" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Persyaratan Pending</p>
                            <p class="text-2xl font-black text-slate-800">{{ statistics.persyaratanPending }}</p>
                        </div>
                    </div>
                </Link>

                <Link :href="route('admin.pembayaran')" class="bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100 hover:shadow-md transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center">
                            <CreditCard class="w-5 h-5 text-rose-600" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pembayaran Pending</p>
                            <p class="text-2xl font-black text-slate-800">{{ statistics.pembayaranPending }}</p>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </CitechDashboardLayout>
</template>

<style scoped>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
<script setup>
import { Head } from '@inertiajs/vue3';
import CitechDashboardLayout from '@/components/CitechDashboardLayout.vue';
</script>

<template>
    <Head title="Dashboard Admin - CITECH 2026" />

    <CitechDashboardLayout activeMenu="admin.dashboard" role="admin">
        <template #header-title>
            <h2 class="text-lg font-black tracking-wide text-slate-800">Beranda Admin</h2>
        </template>

        <div class="space-y-6 animate-fade-in-up">
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-slate-100">
                <h3 class="text-xl font-extrabold text-slate-800">Selamat Datang di Panel Admin CITECH</h3>
                <p class="text-slate-500 text-sm mt-2 leading-relaxed">
                    Gunakan sidebar di sebelah kiri untuk menavigasi menu pengelolaan berkas persyaratan, verifikasi pembayaran, data tim, karya submission, dan penjadwalan tanggal timeline lomba.
                </p>
            </div>
        </div>
    </CitechDashboardLayout>
</template>

<style scoped>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
