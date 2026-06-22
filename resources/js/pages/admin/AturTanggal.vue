<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Calendar, Save } from 'lucide-vue-next';
import CitechDashboardLayout from '@/components/CitechDashboardLayout.vue';

const props = defineProps({
    timelines: Array,
});

const tahapLabels = {
    pendaftaran_b1: 'Pendaftaran Batch 1',
    pendaftaran_b2: 'Pendaftaran Batch 2',
    penyisihan: 'Babak Penyisihan',
    final: 'Babak Final',
    awarding: 'Awarding',
};

const tahapOrder = ['pendaftaran_b1', 'pendaftaran_b2', 'penyisihan', 'final', 'awarding'];

const getTimelineData = (tahap) => {
    const tl = props.timelines?.find(t => t.tahap === tahap);
    return {
        tanggal_mulai: tl?.tanggal_mulai ? tl.tanggal_mulai.substring(0, 16) : '',
        tanggal_selesai: tl?.tanggal_selesai ? tl.tanggal_selesai.substring(0, 16) : '',
    };
};

const formData = {};
tahapOrder.forEach(tahap => {
    const data = getTimelineData(tahap);
    formData[`tanggal_mulai_${tahap}`] = data.tanggal_mulai;
    formData[`tanggal_selesai_${tahap}`] = data.tanggal_selesai;
});

const form = useForm(formData);
const showConfirm = ref(false);

const handleSubmit = () => {
    showConfirm.value = true;
};

const confirmSave = () => {
    showConfirm.value = false;
    form.post(route('admin.atur-tanggal.update'));
};

const cancelSave = () => {
    showConfirm.value = false;
};

const toInputDatetime = (dateStr) => {
    if (!dateStr) return '';
    return dateStr.substring(0, 16);
};
</script>

<template>
    <Head title="Atur Tanggal Timeline - CITECH 2026" />

    <CitechDashboardLayout activeMenu="admin.atur-tanggal" role="admin">
        <template #header-title>
            <h2 class="text-lg font-black tracking-wide text-slate-800">Atur Tanggal</h2>
        </template>

        <div class="space-y-6 animate-fade-in-up">
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-slate-100">
                <h3 class="text-xl font-extrabold text-slate-800">Manajemen Timeline Lomba</h3>
                <p class="text-slate-500 text-sm mt-2 leading-relaxed">
                    Atur tanggal mulai dan tanggal selesai untuk setiap tahapan lomba CITECH. Perubahan akan berlaku langsung setelah disimpan.
                </p>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div v-for="tahap in tahapOrder" :key="tahap"
                    class="bg-white rounded-2xl p-5 md:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-slate-100">
                    <div class="flex items-center gap-2 mb-4">
                        <Calendar class="w-4 h-4 text-blue-600" />
                        <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider">{{ tahapLabels[tahap] }}</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Tanggal Mulai</label>
                            <input type="datetime-local"
                                :value="form[`tanggal_mulai_${tahap}`]"
                                @input="form[`tanggal_mulai_${tahap}`] = $event.target.value"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#1e4d8c] text-sm font-semibold" />
                            <div v-if="form.errors[`tanggal_mulai_${tahap}`]" class="text-xs text-red-500 font-bold mt-1">{{ form.errors[`tanggal_mulai_${tahap}`] }}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Tanggal Selesai</label>
                            <input type="datetime-local"
                                :value="form[`tanggal_selesai_${tahap}`]"
                                @input="form[`tanggal_selesai_${tahap}`] = $event.target.value"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#1e4d8c] text-sm font-semibold" />
                            <div v-if="form.errors[`tanggal_selesai_${tahap}`]" class="text-xs text-red-500 font-bold mt-1">{{ form.errors[`tanggal_selesai_${tahap}`] }}</div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-[#1e4d8c] hover:bg-[#153a6b] text-white font-extrabold text-xs rounded-xl transition shadow-md hover:shadow-lg uppercase tracking-wider disabled:opacity-50">
                        <Save class="w-4 h-4" />
                        Simpan Timeline
                    </button>
                </div>
            </form>
        </div>

        <!-- Confirmation Dialog -->
        <Teleport to="body">
            <div v-if="showConfirm" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/40" @click="cancelSave"></div>
                <div class="relative bg-white rounded-2xl p-6 shadow-2xl max-w-sm w-full mx-4 space-y-4">
                    <h3 class="text-lg font-black text-slate-800">Konfirmasi Perubahan</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Apakah kamu yakin untuk mengubah tanggal submission?</p>
                    <div class="flex gap-3 justify-end">
                        <button @click="cancelSave" class="px-4 py-2 text-sm font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</button>
                        <button @click="confirmSave" class="px-4 py-2 text-sm font-bold text-white bg-[#1e4d8c] rounded-xl hover:bg-[#153a6b] transition">Ya, Simpan</button>
                    </div>
                </div>
            </div>
        </Teleport>
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
    <Head title="Atur Tanggal Timeline - CITECH 2026" />

    <CitechDashboardLayout activeMenu="admin.atur-tanggal" role="admin">
        <template #header-title>
            <h2 class="text-lg font-black tracking-wide text-slate-800">Atur Tanggal</h2>
        </template>

        <div class="space-y-6 animate-fade-in-up">
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-slate-100">
                <h3 class="text-xl font-extrabold text-slate-800">Manajemen Timeline Lomba</h3>
                <p class="text-slate-500 text-sm mt-2 leading-relaxed">
                    Halaman ini digunakan oleh admin untuk mengedit jadwal tanggal mulai dan tanggal selesai di setiap tahapan lomba CITECH.
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
