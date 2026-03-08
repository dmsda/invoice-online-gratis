<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-3">
    <a href="/invoices" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Invoice</a>
</div>

<h4 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Invoice <?= esc($invoice['invoice_number']) ?></h4>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-1"></i><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<?php if($invoice['status'] != 'draft'): ?>
    <div class="alert alert-warning border-warning bg-warning bg-opacity-10">
        <i class="bi bi-exclamation-triangle me-1"></i> Invoice ini statusnya <strong><?= status_label_id($invoice['status'], $invoice['due_date']) ?></strong>. 
        Disarankan hanya mengedit invoice yang masih berstatus Draf.
    </div>
<?php endif; ?>

<form action="/invoices/update/<?= $invoice['uuid'] ?>" method="post" x-data="invoiceForm()" @submit="if(isSaving) { $event.preventDefault(); return; }; isSaving = true;">
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
                                <option :value="client.id" x-text="client.client_name" :selected="client.id == clientId"></option>
                            </template>
                        </select>
                        <small class="text-muted mt-1 d-block">Untuk tambah pelanggan baru, silakan ke menu Pelanggan.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Tagihan Jasa Web" value="<?= esc($invoice['title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Terbit <span class="text-danger">*</span></label>
                        <input type="date" name="date_issued" class="form-control" value="<?= $invoice['date_issued'] ?>" required>
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
                        <input type="date" name="due_date" class="form-control" value="<?= $invoice['due_date'] ?>">
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
                                    <input type="number" x-model="item.quantity" class="form-control" required min="1">
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted">Rp</span>
                                        <input type="number" x-model="item.price" class="form-control border-start-0 ps-1" required min="0">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="d-flex align-items-center justify-content-end h-100">
                                        <span class="fw-bold text-dark">Rp <span x-text="formatNumber(item.quantity * item.price)"></span></span>
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
                                        <input type="number" x-model="item.quantity" class="form-control" required min="1">
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
                                    <span class="fw-bold text-primary">Rp <span x-text="formatNumber(item.quantity * item.price)"></span></span>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <!-- HIDDEN INPUTS FOR SUBMISSION (Prevents Duplicate Mobile/Desktop Submission) -->
                    <template x-for="(item, index) in items" :key="'hidden-'+index">
                        <div class="d-none">
                            <input type="hidden" name="items[item_name][]" :value="item.item_name">
                            <input type="hidden" name="items[description][]" :value="item.description">
                            <input type="hidden" name="items[quantity][]" :value="item.quantity">
                            <input type="hidden" name="items[price][]" :value="item.price">
                        </div>
                    </template>

                    <!-- Add Item Button -->
                    <div class="p-3 border-top" style="background: #f8fafc;">
                        <!-- Warning UX -->
                        <template x-if="items.length > 150">
                            <div class="alert alert-warning py-2 mb-3 d-flex align-items-start shadow-sm border-warning" style="font-size: 0.85rem;">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-2 mt-1"></i>
                                <div>
                                    <strong class="d-block mb-1">⚠️ Perhatian: Fitur Auto Split</strong>
                                    Jika Anda menyimpan perubahan dengan lebih dari 150 item, invoice ini akan <strong class="text-dark">otomatis dipecah</strong> menjadi beberapa invoice lanjutan (Part 1, Part 2, dst).
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
                    <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Transfer ke BCA 123456..."><?= esc($invoice['notes']) ?></textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions mt-4">
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <!-- Delete button — submits external form to avoid nesting -->
                    <button type="submit" form="delete-invoice-form" class="btn btn-outline-danger btn-sm px-3"
                            onclick="return confirm('Yakin hapus invoice <?= esc($invoice['invoice_number']) ?>? Data yang sudah dihapus tidak bisa dikembalikan.');">
                        <i class="bi bi-trash3 me-1"></i>Hapus
                    </button>

                    <div class="d-flex gap-2 flex-grow-1 flex-md-grow-0 justify-content-end">
                        <a href="/invoices" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4" :disabled="isSaving">
                            <span x-show="isSaving" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Delete Invoice Form (outside main form to avoid nesting) -->
<form id="delete-invoice-form" action="/invoices/delete/<?= $invoice['uuid'] ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<script>
    var initialClients = <?= json_encode(array_map(function($c){
        return ['id' => $c['id'], 'client_name' => $c['client_name']];
    }, $clients ?: [])) ?>;

    var existingItems = <?= !empty($items) ? json_encode($items) : '[]' ?>;
    var existingClientId = <?= $invoice['client_id'] ?>;
    var existingDiscount = <?= $invoice['discount'] ?>;
    var existingTax = <?= $invoice['tax'] ?>;
    var existingType = "<?= esc($invoice['type']) ?>";

    function invoiceForm() {
        return {
            clients: initialClients,
            clientId: existingClientId,
            type: existingType || 'produk',
            items: existingItems.map(item => ({
                id: Date.now() + Math.random(),
                item_name: item.item_name,
                description: item.description || '',
                quantity: parseFloat(item.quantity),
                price: parseFloat(item.price)
            })),
            discount: parseFloat(existingDiscount),
            tax: parseFloat(existingTax),
            
            isSaving: false,

            addItem() {
                this.items.push({ id: Date.now() + Math.random(), item_name: '', description: '', quantity: 1, price: 0 });
            },
            confirmRemoveItem(index) {
                if(confirm('Yakin hapus item baris ke-' + (index + 1) + '?')) {
                    this.items.splice(index, 1);
                }
            },
            subtotal() {
                return this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
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
