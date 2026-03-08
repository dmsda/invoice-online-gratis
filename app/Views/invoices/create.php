<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-3">
    <a href="/invoices" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Invoice</a>
</div>

<h4 class="fw-bold mb-4"><i class="bi bi-receipt me-2 text-primary"></i>Buat Invoice Baru</h4>

<form action="/invoices/store" method="post" x-data="invoiceForm()" @submit="if(isSaving) { $event.preventDefault(); return; }; isSaving = true;">
    <?= csrf_field() ?>
    
    <div class="row">
        <!-- HEADER -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <i class="bi bi-info-circle me-1 text-primary"></i>
                    <strong>Info Invoice</strong>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Pelanggan <span class="text-danger">*</span></label>
                        <select name="client_id" class="form-select" x-model="clientId" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            <template x-for="client in clients" :key="client.id">
                                <option :value="client.id" x-text="client.client_name"></option>
                            </template>
                        </select>
                        <small class="text-muted mt-1 d-block">Untuk tambah pelanggan baru, silakan ke menu Pelanggan.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Tagihan Jasa Web" value="INVOICE">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Terbit <span class="text-danger">*</span></label>
                        <input type="date" name="date_issued" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Invoice <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" x-model="type" required>
                            <option value="produk">Produk (Tabel Kuantitas)</option>
                            <option value="jasa">Jasa (Deskripsi Layanan)</option>
                        </select>
                        <small class="text-muted mt-1 d-block">Menentukan format tabel dan PDF.</small>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Jatuh Tempo</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <!-- ITEMS -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header border-0 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);">
                    <div>
                        <i class="bi bi-list-check me-1 text-primary"></i>
                        <strong class="text-dark">Item Tagihan</strong>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill" x-text="items.length + ' item'"></span>
                </div>
                <div class="card-body p-0">
                    <!-- Desktop Header -->
                    <div class="d-none d-md-flex row g-0 border-bottom py-2 px-3 fw-semibold text-muted" style="font-size: 0.8rem; letter-spacing: 0.03em; background: #f8fafc;">
                        <div class="col-md-5">ITEM / JASA</div>
                        <div class="col-md-2">QTY</div>
                        <div class="col-md-3">HARGA SATUAN</div>
                        <div class="col-md-2 text-end">TOTAL</div>
                    </div>

                    <!-- Items Loop -->
                    <template x-for="(item, index) in items" :key="item.id">
                        <div>
                            <!-- Desktop Row -->
                            <div class="d-none d-md-flex row g-2 border-bottom p-3 align-items-start">
                                <div class="col-md-5">
                                    <input type="text" x-model="item.item_name" class="form-control mb-1" placeholder="Nama Item / Jasa" required>
                                    <textarea x-model="item.description" class="form-control form-control-sm text-muted" placeholder="Deskripsi (Opsional)" rows="1"></textarea>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" x-model="item.qty" class="form-control" required min="1">
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted">Rp</span>
                                        <input type="number" x-model="item.price" class="form-control border-start-0 ps-1" required min="0">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="d-flex align-items-center justify-content-end h-100">
                                        <span class="fw-bold text-dark">Rp <span x-text="formatNumber(item.qty * item.price)"></span></span>
                                        <button type="button" class="btn btn-sm text-danger ms-2" 
                                                @click="confirmRemoveItem(index)" x-show="items.length > 1" aria-label="Hapus Item">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Item Card -->
                            <div class="d-md-none invoice-item-card border-bottom p-3 position-relative">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-light text-dark border rounded-pill small" x-text="'Item ' + (index + 1)"></span>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" 
                                            @click="confirmRemoveItem(index)" x-show="items.length > 1" aria-label="Hapus Item">
                                        <i class="bi bi-trash3 me-1"></i><small>Hapus</small>
                                    </button>
                                </div>
                                <div class="mb-2">
                                    <input type="text" x-model="item.item_name" class="form-control" placeholder="Nama Item / Jasa" required>
                                </div>
                                <textarea x-model="item.description" class="form-control form-control-sm text-muted mb-2" placeholder="Deskripsi (Opsional)" rows="1"></textarea>
                                <div class="row g-2 mb-2">
                                    <div class="col-4">
                                        <label class="form-label small text-muted mb-1">Qty</label>
                                        <input type="number" x-model="item.qty" class="form-control" required min="1">
                                    </div>
                                    <div class="col-8">
                                        <label class="form-label small text-muted mb-1">Harga Satuan</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted">Rp</span>
                                            <input type="number" x-model="item.price" class="form-control" required min="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center bg-light rounded-2 px-3 py-2">
                                    <span class="text-muted small">Subtotal</span>
                                    <span class="fw-bold text-primary">Rp <span x-text="formatNumber(item.qty * item.price)"></span></span>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <!-- HIDDEN INPUTS FOR SUBMISSION -->
                    <template x-for="(item, index) in items" :key="'hidden-'+index">
                        <div class="d-none">
                            <input type="hidden" name="items[item_name][]" :value="item.item_name">
                            <input type="hidden" name="items[description][]" :value="item.description">
                            <input type="hidden" name="items[quantity][]" :value="item.qty">
                            <input type="hidden" name="items[price][]" :value="item.price">
                        </div>
                    </template>

                    <!-- Add Item Button -->
                    <div class="p-3 border-top" style="background: #f8fafc;">
                        <!-- Warning UX -->
                        <template x-if="items.length > 150">
                            <div class="alert alert-info py-2 mb-3 d-flex align-items-start shadow-sm border-info" style="font-size: 0.85rem;">
                                <i class="bi bi-info-circle-fill text-info me-2 mt-1"></i>
                                <div>
                                    <strong class="d-block mb-1">ℹ️ Fitur Auto Split Aktif</strong>
                                    Invoice Anda berisi lebih dari 150 item. Saat disimpan, sistem akan <strong class="text-dark">otomatis memecahnya menjadi beberapa invoice terpisah</strong> (maks. 150 baris per file) demi menjaga kerapian dan keamanan dokumen PDF. Anda tidak perlu khawatir!
                                </div>
                            </div>
                        </template>

                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" @click="addItem()">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Item Baru
                        </button>
                    </div>
                </div>
                
                <!-- Footer Totals -->
                <div class="card-footer border-0 pt-3" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);">
                    <div class="invoice-totals-form ms-auto">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <strong>Rp <span x-text="formatNumber(subtotal())"></span></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <span class="text-muted">Diskon:</span>
                            <div class="input-group input-group-sm" style="max-width: 180px;">
                                <span class="input-group-text bg-white border">Rp</span>
                                <input type="number" name="discount" x-model="discount" class="form-control text-end">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <span class="text-muted">Pajak:</span>
                            <div class="input-group input-group-sm" style="max-width: 180px;">
                                <span class="input-group-text bg-white border">Rp</span>
                                <input type="number" name="tax" x-model="tax" class="form-control text-end">
                            </div>
                        </div>
                        <hr class="my-3 border-primary border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark">Total:</h5>
                            <h4 class="mb-0 fw-bold text-primary">Rp <span x-text="formatNumber(grandTotal())"></span></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3 shadow-sm border-0">
                <div class="card-body">
                    <label class="form-label fw-semibold"><i class="bi bi-sticky me-1 text-muted"></i>Catatan / Footer Invoice</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Transfer ke BCA 123456..."></textarea>
                </div>
            </div>

            <!-- Submit -->
            <!-- Desktop Actions (Hidden on Mobile) -->
            <div class="form-actions mt-4 d-none d-md-block">
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <a href="/invoices" class="btn btn-light px-4">Batal</a>
                    <button type="submit" class="btn btn-lg btn-primary rounded-pill px-4 flex-grow-1 flex-md-grow-0" :disabled="isSaving">
                        <span x-show="isSaving" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        <span x-show="!isSaving"><i class="bi bi-check-lg me-2"></i>Simpan Invoice</span>
                        <span x-show="isSaving">Menyimpan...</span>
                    </button>
                </div>
                <small class="text-center mt-2 text-muted d-block">Invoice tersimpan sebagai Draf. Anda bisa kirim nanti.</small>
            </div>

            <!-- Mobile Sticky Submit Bar (Visible on Mobile) -->
            <div class="sticky-bottom-bar d-md-none d-flex justify-content-between align-items-center gap-3">
                <div class="d-flex flex-column lh-1">
                    <span class="text-muted small" style="font-size: 0.7rem;">Total Tagihan</span>
                    <span class="fw-bold text-dark fs-6">Rp <span x-text="formatNumber(grandTotal())"></span></span>
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-4" :disabled="isSaving">
                    <span x-show="isSaving" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span x-show="!isSaving"><i class="bi bi-check-lg me-1"></i>Simpan</span>
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    var initialClients = <?= json_encode(array_map(function($c){
        return ['id' => $c['id'], 'client_name' => $c['client_name']];
    }, $clients)) ?>;
</script>


<script>
    function invoiceForm() {
        return {
            clients: initialClients,
            clientId: '',
            type: 'produk',
            items: [
                { id: Date.now() + Math.random(), qty: 1, price: 0 }
            ],
            discount: 0,
            tax: 0,
            
            isSaving: false,

            addItem() {
                this.items.push({ id: Date.now() + Math.random(), qty: 1, price: 0 });
            },
            confirmRemoveItem(index) {
                if(confirm('Yakin hapus item baris ke-' + (index + 1) + '?')) {
                    this.items.splice(index, 1);
                }
            },
            subtotal() {
                return this.items.reduce((sum, item) => sum + (item.qty * item.price), 0);
            },
            grandTotal() {
                return this.subtotal() - this.discount + parseInt(this.tax || 0);
            },
            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }
        }
    }
</script>
<?= $this->endSection() ?>
