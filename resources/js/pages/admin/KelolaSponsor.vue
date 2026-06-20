<script setup>
import { Head } from '@inertiajs/vue3';
import CitechDashboardLayout from '@/components/CitechDashboardLayout.vue';
</script>

<template>
    <Head title="Kelola Sponsor - CITECH 2026" />

    <CitechDashboardLayout activeMenu="admin.kelola-sponsor" role="admin">
        <template #header-title>
            <h2 class="text-lg font-black tracking-wide text-slate-800">Kelola Sponsor</h2>
        </template>

        <div class="space-y-6 animate-fade-in-up">
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-slate-100">
                <h3 class="text-xl font-extrabold text-slate-800">Manajemen Sponsor & Partnership</h3>
                <p class="text-slate-500 text-sm mt-2 leading-relaxed">
                    Halaman ini digunakan oleh admin untuk mengelola data sponsor, logo mitra teknologi, tingkat sponsorship, dan kerja sama event CITECH.
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
