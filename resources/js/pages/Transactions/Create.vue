<script setup lang="ts">
import { onKeyStroke } from '@vueuse/core';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useFormatRupiah } from '@/composables/useFormatRupiah';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import {
    Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter
} from '@/components/ui/dialog';
import { Skeleton } from '@/components/ui/skeleton';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import {
    Trash2, ShoppingCart, Plus, Save, User, FileText,
    RefreshCw, Zap, Keyboard, Loader2, AlertTriangle, UserPlus, Tag, Percent, Search,
    X, Minus, CheckCircle2, ChevronRight, Calculator, CreditCard, Banknote, QrCode
} from 'lucide-vue-next';

// ============================================================
// Props & Axiom Context
// ============================================================
const props = defineProps<{
    services: any[];
    variants: any[];
}>();

const axiom = computed(() => usePage().props.axiom as any || {
    labels: {
        variant: 'Varian',
        attribute: 'Tipe Opsi',
        attribute_values: { standar: 'Standar' },
        dimension_width: 'Lebar',
        dimension_height: 'Tinggi',
        dimension_unit: 'm'
    }
});

const { formatRupiah, formatAngka } = useFormatRupiah();

// ============================================================
// Customer Async Search
// ============================================================
interface CustomerOption {
    id: number;
    name: string;
    phone: string;
}

const customerSearchQuery = ref('');
const customerSearchResults = ref<CustomerOption[]>([]);
const isSearchingCustomer = ref(false);
const isCustomerDropdownOpen = ref(false);
const selectedCustomerLabel = ref('Pelanggan Umum');

let customerSearchTimer: ReturnType<typeof setTimeout>;
const autoSelectAfterSearch = ref(false);

const searchCustomers = (query: string) => {
    clearTimeout(customerSearchTimer);
    if (!query.trim()) {
        customerSearchResults.value = [];
        return;
    }
    isSearchingCustomer.value = true;
    customerSearchTimer = setTimeout(async () => {
        try {
            const response = await fetch(`/customers/search?q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (response.ok) {
                customerSearchResults.value = await response.json();
                if (autoSelectAfterSearch.value && customerSearchResults.value.length > 0) {
                    selectCustomer(customerSearchResults.value[0]);
                    autoSelectAfterSearch.value = false;
                }
            }
        } catch (error) {
            console.error('Fetch customers error:', error);
        } finally {
            isSearchingCustomer.value = false;
        }
    }, 300);
};

const selectCustomer = (customer: CustomerOption) => {
    form.customer_id = customer.id.toString();
    selectedCustomerLabel.value = `${customer.name} — ${customer.phone}`;
    customerSearchQuery.value = '';
    customerSearchResults.value = [];
    isCustomerDropdownOpen.value = false;
};

const clearCustomer = () => {
    form.customer_id = 'none';
    selectedCustomerLabel.value = 'Pelanggan Umum';
    customerSearchQuery.value = '';
    customerSearchResults.value = [];
    isCustomerDropdownOpen.value = false;
};

watch(customerSearchQuery, (newQuery) => searchCustomers(newQuery));

const customerComboboxRef = ref<HTMLElement | null>(null);
const handleClickOutside = (event: MouseEvent) => {
    if (customerComboboxRef.value && !customerComboboxRef.value.contains(event.target as Node)) {
        isCustomerDropdownOpen.value = false;
    }
};

onMounted(() => document.addEventListener('mousedown', handleClickOutside));
onUnmounted(() => document.removeEventListener('mousedown', handleClickOutside));

// ============================================================
// Form State
// ============================================================
const form = useForm({
    customer_id: 'none',
    items: [] as Array<{
        service_id: number;
        variant_id: number | null;
        attribute: string;
        qty: number;
        unit_price: number;
        item_notes: string;
        file: File | null;
        width: number | undefined;
        height: number | undefined;
    }>,
    discount_type: 'percent' as 'percent' | 'flat',
    discount_value: 0,
    payment_method: 'cash',
    amount_paid: 0,
    notes: '',
});

const selectedServiceId = ref('');
const serviceSearchQuery = ref('');
const filteredServices = computed(() => {
    if (!serviceSearchQuery.value) return props.services;
    const q = serviceSearchQuery.value.toLowerCase();
    return props.services.filter(s => s.name.toLowerCase().includes(q));
});

const pinnedServices = computed(() => {
    return props.services.filter(s => s.is_pinned).slice(0, 8);
});

// ============================================================
// Item Operations
// ============================================================
const addItem = (service: any) => {
    const defaultVariant = service.has_matrix_pricing && service.prices.length > 0 
        ? service.prices[0].variant_id 
        : (props.variants.length > 0 ? props.variants[0].id : null);
    
    const defaultAttribute = service.has_matrix_pricing && service.prices.length > 0 
        ? service.prices[0].attribute 
        : 'na';

    form.items.push({
        service_id: service.id,
        variant_id: defaultVariant,
        attribute: defaultAttribute,
        qty: 1,
        unit_price: Number(service.base_price) || 0,
        item_notes: '',
        file: null,
        width: service.is_custom_size ? 1 : undefined,
        height: service.is_custom_size ? 1 : undefined,
    });
    
    updateItemPrice(form.items.length - 1);
};

const updateItemPrice = (index: number) => {
    const item = form.items[index];
    const service = props.services.find(s => s.id === item.service_id);

    if (service?.is_custom_size) {
        const w = Number(item.width) || 0;
        const h = Number(item.height) || 0;
        item.unit_price = parseFloat(service.base_price) * w * h;
    } else if (service?.has_matrix_pricing) {
        const pricing = service.prices.find((p: any) =>
            p.variant_id == item.variant_id && p.attribute == item.attribute
        );
        if (pricing) {
            item.unit_price = pricing.price;
        }
    } else {
        item.unit_price = Number(service?.base_price) || 0;
    }
};

const removeItem = (index: number) => {
    form.items.splice(index, 1);
};

const incrementQty = (index: number) => {
    form.items[index].qty++;
};

const decrementQty = (index: number) => {
    if (form.items[index].qty > 1) form.items[index].qty--;
};

// ============================================================
// Calculations
// ============================================================
const subtotal = computed(() =>
    form.items.reduce((sum, item) => sum + (item.unit_price * item.qty), 0)
);

const discountAmount = computed(() => {
    if (form.discount_type === 'percent') {
        return subtotal.value * (form.discount_value / 100);
    }
    return Math.min(form.discount_value, subtotal.value);
});

const totalFinal = computed(() => subtotal.value - discountAmount.value);
const changeAmount = computed(() => form.amount_paid - totalFinal.value);
const isUnderpaid = computed(() => form.payment_method === 'cash' && form.amount_paid > 0 && changeAmount.value < 0);

// ============================================================
// Dialogs & Actions
// ============================================================
const showAddCustomerDialog = ref(false);
const newCustomerForm = useForm({
    name: '',
    phone: '',
    address: '',
    notes: '',
});

const submitNewCustomer = () => {
    newCustomerForm.post(route('customers.store'), {
        onSuccess: () => {
            const name = newCustomerForm.name;
            showAddCustomerDialog.value = false;
            newCustomerForm.reset();
            autoSelectAfterSearch.value = true;
            customerSearchQuery.value = name;
        }
    });
};

const showConfirmDialog = ref(false);
const checkoutAttempted = ref(false);
const openConfirmDialog = () => {
    checkoutAttempted.value = true;
    if (form.items.length === 0 || form.customer_id === 'none' || isUnderpaid.value) return;
    showConfirmDialog.value = true;
};

const submitTransaction = () => {
    showConfirmDialog.value = false;
    form.post(route('transactions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.items = [];
            clearCustomer();
        }
    });
};

const cashQuickAmounts = [50000, 100000, 200000, 500000];
const setQuickCash = (amount: number) => {
    form.amount_paid = amount;
};

// Keyboard Shortcuts
onKeyStroke('F2', (e) => { e.preventDefault(); document.getElementById('service-search')?.focus(); });
onKeyStroke('F4', (e) => { e.preventDefault(); document.getElementById('amount-paid')?.focus(); });
onKeyStroke('F9', (e) => { e.preventDefault(); openConfirmDialog(); });

</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: route('dashboard') }, { title: 'Kasir', href: route('transactions.create') }]">
        <Head title="Point of Sale" />

        <div class="flex flex-col h-[calc(100vh-120px)] overflow-hidden gap-4 p-4 lg:p-6">
            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-full overflow-hidden">
                
                <!-- Left Section: Service Selection & Cart (8 cols) -->
                <div class="lg:col-span-8 flex flex-col gap-4 overflow-hidden">
                    
                    <!-- Header Bar & Service Selection -->
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h1 class="text-2xl font-bold tracking-tight bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent">Point of Sale</h1>
                            <Button variant="outline" size="sm" @click="form.reset(); form.items = []" class="rounded-xl border-slate-200">
                                <RefreshCw class="w-4 h-4 mr-2" /> Reset
                            </Button>
                        </div>

                        <!-- Search & Pinned -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="relative group">
                                <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-primary transition-colors" />
                                <Select v-model="selectedServiceId" @update:model-value="(val) => { if(val) { addItem(props.services.find(s => s.id == val)); selectedServiceId = '' } }">
                                    <SelectTrigger id="service-search" class="w-full h-12 pl-10 rounded-2xl border-slate-200 bg-white/50 backdrop-blur-sm shadow-sm transition-all focus:ring-4 focus:ring-primary/10 hover:border-primary/30">
                                        <SelectValue placeholder="Cari atau pilih layanan... (F2)" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="s in services" :key="s.id" :value="s.id.toString()">{{ s.name }}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                                <Button v-for="s in pinnedServices" :key="s.id" variant="secondary" size="sm" @click="addItem(s)" class="rounded-xl h-10 px-4 bg-slate-100 hover:bg-primary hover:text-white transition-all shrink-0">
                                    <Zap class="w-3.5 h-3.5 mr-2" /> {{ s.name }}
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Area -->
                    <Card class="flex-1 flex flex-col overflow-hidden rounded-3xl border-slate-200/60 shadow-xl bg-white/80 backdrop-blur-md">
                        <CardHeader class="px-6 py-4 border-b border-slate-100 flex-row items-center justify-between">
                            <CardTitle class="text-base font-semibold text-slate-800 flex items-center">
                                <ShoppingCart class="w-5 h-5 mr-2 text-primary" />
                                Keranjang Belanja <Badge variant="secondary" class="ml-2 rounded-lg bg-primary/10 text-primary">{{ form.items.length }} Items</Badge>
                            </CardTitle>
                        </CardHeader>
                        
                        <CardContent class="flex-1 overflow-y-auto p-0 scrollbar-thin">
                            <div v-if="form.items.length === 0" class="flex flex-col items-center justify-center h-full opacity-40 py-20">
                                <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                    <ShoppingCart class="w-10 h-10 text-slate-300" />
                                </div>
                                <p class="text-sm font-medium text-slate-500">Belum ada item ditambahkan</p>
                            </div>

                            <div v-else class="divide-y divide-slate-100">
                                <div v-for="(item, index) in form.items" :key="index" class="p-6 transition-all hover:bg-slate-50/50 group">
                                    <div class="flex items-start gap-4">
                                        <!-- Service Icon/Initial -->
                                        <div class="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center text-primary font-bold text-lg shrink-0">
                                            {{ services.find(s => s.id == item.service_id)?.name.charAt(0) }}
                                        </div>

                                        <!-- Details -->
                                        <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                                            <div class="md:col-span-5 space-y-1">
                                                <h3 class="font-bold text-slate-900">{{ services.find(s => s.id == item.service_id)?.name }}</h3>
                                                <div class="flex flex-wrap gap-2 pt-1">
                                                    <!-- Layout Per-Meter (Issue #11) -->
                                                    <template v-if="services.find(s => s.id == item.service_id)?.is_custom_size">
                                                        <div class="flex items-center gap-1.5 bg-slate-100 rounded-lg px-2 py-1">
                                                            <Input v-model="item.width" type="number" step="0.1" @change="updateItemPrice(index)" class="w-16 h-7 text-xs border-0 bg-transparent p-0 text-center font-bold" />
                                                            <span class="text-[10px] text-slate-400">×</span>
                                                            <Input v-model="item.height" type="number" step="0.1" @change="updateItemPrice(index)" class="w-16 h-7 text-xs border-0 bg-transparent p-0 text-center font-bold" />
                                                            <span class="text-[10px] text-slate-400">{{ axiom.labels.dimension_unit }}</span>
                                                        </div>
                                                    </template>

                                                    <!-- Matrix Pricing -->
                                                    <template v-else-if="services.find(s => s.id == item.service_id)?.has_matrix_pricing">
                                                        <Select v-model="item.variant_id" @update:model-value="updateItemPrice(index)">
                                                            <SelectTrigger class="h-8 text-xs rounded-lg w-auto min-w-[100px] border-slate-200">
                                                                <SelectValue :placeholder="axiom.labels.variant" />
                                                            </SelectTrigger>
                                                            <SelectContent>
                                                                <SelectItem v-for="v in variants" :key="v.id" :value="v.id">{{ v.name }}</SelectItem>
                                                            </SelectContent>
                                                        </Select>

                                                        <Select v-model="item.attribute" @update:model-value="updateItemPrice(index)">
                                                            <SelectTrigger class="h-8 text-xs rounded-lg w-auto min-w-[100px] border-slate-200">
                                                                <SelectValue :placeholder="axiom.labels.attribute" />
                                                            </SelectTrigger>
                                                            <SelectContent>
                                                                <SelectItem v-for="(v, k) in axiom.labels?.attribute_values || {}" :key="k" :value="k">{{ v }}</SelectItem>
                                                            </SelectContent>
                                                        </Select>
                                                    </template>
                                                </div>
                                            </div>

                                            <!-- Qty Control -->
                                            <div class="md:col-span-3">
                                                <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl w-fit">
                                                    <Button variant="ghost" size="icon" @click="decrementQty(index)" class="h-8 w-8 rounded-lg hover:bg-white text-slate-600"><Minus class="w-4 h-4" /></Button>
                                                    <Input v-model="item.qty" type="number" min="1" class="w-12 h-8 border-0 bg-transparent p-0 text-center font-bold text-sm" />
                                                    <Button variant="ghost" size="icon" @click="incrementQty(index)" class="h-8 w-8 rounded-lg hover:bg-white text-primary"><Plus class="w-4 h-4" /></Button>
                                                </div>
                                            </div>

                                            <div class="md:col-span-4 text-right">
                                                <p class="text-xs text-slate-400 mb-0.5">{{ formatRupiah(item.unit_price) }}</p>
                                                <p class="font-bold text-lg text-slate-900">{{ formatRupiah(item.unit_price * item.qty) }}</p>
                                            </div>
                                        </div>

                                        <Button variant="ghost" size="icon" @click="removeItem(index)" class="h-10 w-10 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-xl">
                                            <Trash2 class="w-5 h-5" />
                                        </Button>
                                    </div>
                                    <div class="mt-4 pl-16">
                                        <Input v-model="item.item_notes" placeholder="Catatan khusus item..." class="h-9 rounded-xl border-dashed border-slate-200 text-xs text-slate-500 focus:border-primary/50" />
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Section: Checkout (4 cols) -->
                <div class="lg:col-span-4 flex flex-col gap-6">
                    <!-- Customer Selection -->
                    <Card class="rounded-3xl border-slate-200/60 shadow-lg overflow-visible">
                        <CardHeader class="pb-3 flex-row items-center justify-between">
                            <CardTitle class="text-sm font-bold uppercase tracking-wider text-slate-500">Sesi Pelanggan</CardTitle>
                            <Button variant="ghost" size="sm" @click="showAddCustomerDialog = true" class="text-primary text-xs font-semibold h-7"><UserPlus class="w-3.5 h-3.5 mr-1" /> Baru</Button>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div ref="customerComboboxRef" class="relative">
                                <button type="button" @click="isCustomerDropdownOpen = !isCustomerDropdownOpen" class="w-full flex items-center justify-between h-14 rounded-2xl border border-slate-200 bg-white px-4 text-left transition-all hover:border-primary/50 focus:ring-2 focus:ring-primary/20">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500"><User class="w-4 h-4" /></div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 leading-tight">{{ form.customer_id === 'none' ? 'Pilih Pelanggan' : selectedCustomerLabel }}</p>
                                            <p class="text-[10px] text-slate-500 mt-0.5">{{ form.customer_id === 'none' ? 'Klik untuk cari...' : 'Terpilih' }}</p>
                                        </div>
                                    </div>
                                    <ChevronRight class="w-4 h-4 text-slate-400 transition-transform" :class="isCustomerDropdownOpen ? 'rotate-90' : ''" />
                                </button>

                                <!-- Dropdown UI -->
                                <div v-if="isCustomerDropdownOpen" class="absolute z-[60] mt-2 w-full bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden p-2">
                                    <div class="relative mb-2">
                                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                                        <Input v-model="customerSearchQuery" placeholder="Cari nama atau telepon..." class="h-10 pl-10 rounded-xl" autofocus />
                                    </div>
                                    <div class="max-h-60 overflow-y-auto space-y-1">
                                        <button v-for="c in customerSearchResults" :key="c.id" @click="selectCustomer(c)" class="w-full text-left p-3 rounded-xl hover:bg-slate-50 flex items-center gap-3 transition-colors">
                                            <div class="w-7 h-7 rounded-lg bg-primary/5 text-primary flex items-center justify-center text-xs font-bold">{{ c.name.charAt(0) }}</div>
                                            <div>
                                                <p class="text-sm font-semibold">{{ c.name }}</p>
                                                <p class="text-[10px] text-slate-500">{{ c.phone }}</p>
                                            </div>
                                        </button>
                                        <div v-if="customerSearchResults.length === 0 && customerSearchQuery" class="p-4 text-center">
                                            <p class="text-xs text-slate-400">Tidak ada hasil. <button @click="showAddCustomerDialog = true" class="text-primary font-bold">Daftarkan baru?</button></p>
                                        </div>
                                        <button @click="clearCustomer" class="w-full text-left p-3 rounded-xl hover:bg-slate-50 text-red-500 text-xs font-bold border-t border-slate-100 mt-2 flex items-center gap-2">
                                            <X class="w-3.5 h-3.5" /> Batalkan Pilihan
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-if="checkoutAttempted && form.customer_id === 'none'" class="flex items-center gap-2 text-red-500 text-[11px] font-medium bg-red-50 p-2 rounded-xl">
                                <AlertTriangle class="w-3.5 h-3.5" /> Pilih pelanggan terlebih dahulu!
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Summary Card -->
                    <Card class="flex-1 rounded-3xl border-0 shadow-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white flex flex-col overflow-hidden">
                        <CardHeader class="pb-2">
                            <CardTitle class="text-xs font-bold uppercase tracking-widest text-slate-400">Ringkasan Pembayaran</CardTitle>
                        </CardHeader>
                        <CardContent class="flex-1 flex flex-col p-6 space-y-6">
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm text-slate-400">
                                    <span>Subtotal ({{ form.items.length }} Items)</span>
                                    <span>{{ formatRupiah(subtotal) }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-4 py-2 border-y border-white/10">
                                    <div class="flex items-center gap-2">
                                        <Tag class="w-4 h-4 text-primary" />
                                        <Select v-model="form.discount_type" class="bg-transparent border-0 text-white">
                                            <SelectTrigger class="h-6 w-20 bg-transparent border-0 text-white p-0 text-sm">
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="percent">Persen %</SelectItem>
                                                <SelectItem value="flat">Rupiah Rp</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div class="flex-1 max-w-[100px]">
                                        <Input v-model="form.discount_value" type="number" class="h-8 bg-white/10 border-0 text-right font-bold focus:ring-1 focus:ring-primary" placeholder="0" />
                                    </div>
                                </div>
                                <div class="pt-4 flex flex-col gap-1 text-center">
                                    <p class="text-xs text-slate-400 font-medium">TOTAL AKHIR</p>
                                    <p class="text-4xl font-black text-white tracking-tight">{{ formatRupiah(totalFinal) }}</p>
                                </div>
                            </div>

                            <Separator class="bg-white/10" />

                            <!-- Payment Section -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-3 gap-2">
                                    <button @click="form.payment_method = 'cash'" :class="form.payment_method === 'cash' ? 'bg-primary text-white' : 'bg-white/10 text-slate-400'" class="flex flex-col items-center justify-center h-20 rounded-2xl transition-all hover:bg-white/20">
                                        <Banknote class="w-6 h-6 mb-1" />
                                        <span class="text-[10px] font-bold uppercase">Tunai</span>
                                    </button>
                                    <button @click="form.payment_method = 'qris'" :class="form.payment_method === 'qris' ? 'bg-primary text-white' : 'bg-white/10 text-slate-400'" class="flex flex-col items-center justify-center h-20 rounded-2xl transition-all hover:bg-white/20">
                                        <QrCode class="w-6 h-6 mb-1" />
                                        <span class="text-[10px] font-bold uppercase">QRIS</span>
                                    </button>
                                    <button @click="form.payment_method = 'transfer'" :class="form.payment_method === 'transfer' ? 'bg-primary text-white' : 'bg-white/10 text-slate-400'" class="flex flex-col items-center justify-center h-20 rounded-2xl transition-all hover:bg-white/20">
                                        <CreditCard class="w-6 h-6 mb-1" />
                                        <span class="text-[10px] font-bold uppercase">Tf</span>
                                    </button>
                                </div>

                                <div v-if="form.payment_method === 'cash'" class="space-y-3 animate-in fade-in slide-in-from-top-2">
                                    <div class="relative">
                                        <Label class="text-[10px] text-slate-400 mb-1 inline-block">NOMINAL BAYAR (F4)</Label>
                                        <Input id="amount-paid" v-model="form.amount_paid" type="number" placeholder="Masukkan Rp..." class="h-14 bg-white/10 border-0 text-xl font-bold text-primary focus:ring-2 focus:ring-primary shadow-inner rounded-2xl" />
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <Button v-for="amt in cashQuickAmounts" :key="amt" variant="secondary" size="sm" @click="setQuickCash(amt)" class="bg-white/5 border-0 hover:bg-white/20 text-xs h-9 rounded-xl">{{ formatRupiah(amt) }}</Button>
                                    </div>
                                    <div v-if="changeAmount !== 0" class="flex justify-between items-center p-4 rounded-2xl" :class="changeAmount >= 0 ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300'">
                                        <span class="text-xs font-bold uppercase">{{ changeAmount >= 0 ? 'Kembalian' : 'Kurang' }}</span>
                                        <span class="text-lg font-black">{{ formatRupiah(Math.abs(changeAmount)) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto pt-6">
                                <Button @click="openConfirmDialog" :disabled="form.items.length === 0 || form.processing" class="w-full h-16 rounded-2xl text-lg font-black shadow-2xl transition-all hover:scale-[1.02] active:scale-95 group overflow-hidden relative" :class="isUnderpaid ? 'bg-slate-700 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600'">
                                    <div class="flex items-center relative z-10">
                                        <Loader2 v-if="form.processing" class="w-6 h-6 mr-2 animate-spin" />
                                        <ShoppingCart v-else class="w-6 h-6 mr-2 group-hover:rotate-12 transition-transform" />
                                        PROSES PEMBAYARAN (F9)
                                    </div>
                                    <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent opacity-50 skew-x-12 translate-x-full group-hover:translate-x-[-150%] transition-transform duration-700"></div>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Add Customer Dialog -->
        <Dialog v-model:open="showAddCustomerDialog">
            <DialogContent class="rounded-3xl border-slate-200">
                <DialogHeader>
                    <DialogTitle class="text-xl font-bold">Pelanggan Baru</DialogTitle>
                    <DialogDescription>Daftarkan data pelanggan langsung ke sistem master.</DialogDescription>
                </DialogHeader>
                <div class="space-y-4 py-4">
                    <div class="space-y-2">
                        <Label>Nama Lengkap</Label>
                        <Input v-model="newCustomerForm.name" placeholder="Contoh: Budi Sudarsono" class="rounded-xl h-12" />
                    </div>
                    <div class="space-y-2">
                        <Label>Nomor WhatsApp</Label>
                        <Input v-model="newCustomerForm.phone" placeholder="08xxxx" class="rounded-xl h-12" />
                    </div>
                    <div class="space-y-2">
                        <Label>Alamat (Opsional)</Label>
                        <Input v-model="newCustomerForm.address" placeholder="Kota/Kecamatan..." class="rounded-xl h-12" />
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="showAddCustomerDialog = false" class="rounded-xl">Batal</Button>
                    <Button @click="submitNewCustomer" :disabled="!newCustomerForm.name || newCustomerForm.processing" class="rounded-xl px-8">
                        <Loader2 v-if="newCustomerForm.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Simpan & Pilih
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Final Confirmation Dialog -->
        <Dialog v-model:open="showConfirmDialog">
            <DialogContent class="rounded-3xl border-slate-200 p-0 overflow-hidden max-w-md">
                <div class="p-8 text-center space-y-4">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto shadow-lg shadow-blue-500/30">
                        <CheckCircle2 class="text-white w-8 h-8" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 leading-tight">Finalisasi Transaksi?</h3>
                        <p class="text-slate-500 text-sm mt-2 font-medium">Data akan disimpan secara permanen dan nota siap dicetak.</p>
                    </div>
                </div>
                <div class="bg-slate-50 p-8 space-y-4 flex flex-col gap-2">
                    <div class="flex justify-between items-center text-sm font-bold text-slate-600 bg-white p-4 rounded-2xl shadow-sm">
                        <span>Tagihan Total</span>
                        <span class="text-lg text-slate-900">{{ formatRupiah(totalFinal) }}</span>
                    </div>
                    <div v-if="form.payment_method === 'cash'" class="flex justify-between items-center text-sm font-bold text-slate-600 bg-white p-4 rounded-2xl shadow-sm">
                        <span>Bayar Tunai</span>
                        <span class="text-lg text-primary">{{ formatRupiah(form.amount_paid) }}</span>
                    </div>
                    
                    <div class="flex gap-3 pt-4">
                        <Button variant="outline" @click="showConfirmDialog = false" class="flex-1 h-12 rounded-2xl border-slate-200 font-bold">Batal</Button>
                        <Button @click="submitTransaction" class="flex-1 h-12 rounded-2xl bg-blue-500 hover:bg-blue-600 font-black shadow-lg shadow-blue-500/20">Konfirmasi!</Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
