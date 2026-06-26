<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';

const passwordInput = useTemplateRef('passwordInput');
</script>

<template>
    <div class="space-y-6">
        <div class="pb-2">
            <h3 class="text-lg font-extrabold text-slate-800">Hapus Akun</h3>
            <p class="mt-1 text-xs font-bold text-slate-400">
                Hapus akun Anda beserta seluruh data di dalamnya secara
                permanen.
            </p>
        </div>

        <div
            class="space-y-4 rounded-2xl border border-red-100 bg-red-50/40 p-6"
        >
            <div class="relative space-y-1 text-red-700">
                <p class="text-sm font-black tracking-wide uppercase">
                    Peringatan Penting
                </p>
                <p class="text-xs font-bold text-red-500">
                    Tindakan ini tidak dapat dibatalkan. Seluruh data tim,
                    berkas persyaratan, bukti pembayaran, dan karya yang telah
                    diunggah akan dihapus secara permanen dari sistem.
                </p>
            </div>
            <Dialog>
                <DialogTrigger as-child>
                    <Button
                        variant="destructive"
                        class="flex h-11! items-center justify-center rounded-xl bg-red-600 px-6! py-3! text-xs font-black tracking-wider text-white shadow-md transition hover:bg-red-700"
                        data-test="delete-user-button"
                    >
                        Hapus Akun Saya
                    </Button>
                </DialogTrigger>
                <DialogContent
                    class="rounded-3xl border border-slate-100 bg-white p-6 shadow-2xl sm:max-w-md"
                >
                    <Form
                        v-bind="ProfileController.destroy.form()"
                        reset-on-success
                        @error="() => passwordInput?.focus()"
                        :options="{
                            preserveScroll: true,
                        }"
                        class="space-y-6"
                        v-slot="{ errors, processing, reset, clearErrors }"
                    >
                        <DialogHeader class="space-y-3">
                            <DialogTitle
                                class="text-lg font-extrabold text-slate-800"
                                >Apakah Anda yakin ingin menghapus
                                akun?</DialogTitle
                            >
                            <DialogDescription
                                class="text-xs leading-relaxed font-bold text-slate-400"
                            >
                                Setelah akun Anda dihapus, semua data Anda akan
                                hilang secara permanen. Silakan masukkan
                                password Anda untuk mengonfirmasi bahwa Anda
                                ingin menghapus akun ini secara permanen.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="password" class="sr-only"
                                >Password</Label
                            >
                            <PasswordInput
                                id="password"
                                name="password"
                                ref="passwordInput"
                                class="h-11! w-full rounded-xl! border-slate-200! px-4! py-3! text-sm! font-semibold! focus:ring-2! focus:ring-red-600! focus:outline-none!"
                                placeholder="Masukkan password Anda"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <DialogFooter
                            class="flex items-center justify-end gap-3"
                        >
                            <DialogClose as-child>
                                <Button
                                    variant="secondary"
                                    class="h-11! rounded-xl border border-slate-200 bg-white px-6! py-3! text-xs font-black tracking-wider text-slate-700 shadow-sm transition hover:bg-slate-50"
                                    @click="
                                        () => {
                                            clearErrors();
                                            reset();
                                        }
                                    "
                                >
                                    Batal
                                </Button>
                            </DialogClose>

                            <Button
                                type="submit"
                                variant="destructive"
                                class="h-11! rounded-xl bg-red-600 px-6! py-3! text-xs font-black tracking-wider text-white shadow-md transition hover:bg-red-700"
                                :disabled="processing"
                                data-test="confirm-delete-user-button"
                            >
                                Hapus Akun
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>
