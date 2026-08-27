<template>
    <!-- List Data -->
    <div class="col-lg-12">
        <div class="row">
            <div class="col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body d-flex justify-content-start p-2">
                        <div class="me-3">
                            <label class="form-label">Tanggal </label>
                            <div
                                class="btn-group btn-group2 w-100"
                                role="group"
                            >
                                <VueCtkDateTimePicker
                                    label="Filter Tanggal"
                                    locale="Asia/Jakarta"
                                    class="form-control"
                                    v-model="filter.date"
                                    @validate="filterDate"
                                    :range="true"
                                />
                                <button
                                    type="button"
                                    v-tooltip.top="'Hapus Filter'"
                                    @click="removeFilter('date')"
                                    class="btn btn-outline-danger btn-wave waves-effect waves-light"
                                >
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="me-3">
                            <label class="form-label">Akun </label>
                            <div
                                class="btn-group btn-group2 w-100"
                                role="group"
                            >
                                <Multiselect
                                    v-model="filter.account"
                                    :options="accounts"
                                    :multiple="false"
                                    :close-on-select="true"
                                    @select="getData"
                                    :clear-on-select="true"
                                    :preserve-search="true"
                                    :searchable="true"
                                    :internal-search="false"
                                    :options-limit="50"
                                    :loading="loader.account"
                                    placeholder="Pilih Akun"
                                    open-direction="bottom"
                                    style="width: 250px !important"
                                    label="name"
                                    id="id"
                                    track-by="name"
                                    @search-change="getAccount"
                                ></Multiselect>
                                <button
                                    type="button"
                                    v-tooltip.top="'Hapus Filter'"
                                    @click="removeFilter('account')"
                                    class="btn btn-outline-danger btn-wave waves-effect waves-light"
                                >
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button
                            type="button"
                            v-tooltip.top="'Refresh'"
                            @click="getData()"
                            class="btn btn-outline-info btn-wave waves-effect waves-light mt-6 me-3"
                        >
                            <i class="fa fa-refresh"></i>
                        </button>
                        <button
                            v-if="import_file"
                            type="button"
                            @click="modal.import = true"
                            v-tooltip.top="'Import File'"
                            class="btn btn-outline-info btn-wave waves-effect waves-light mt-6 me-3"
                        >
                            <i class="fa fa-upload"></i>
                        </button>

                        <button
                            v-if="import_file"
                            type="button"
                            @click="
                                getData(
                                    1,
                                    type == 'jurnal' ? 'mutasi' : 'jurnal'
                                )
                            "
                            v-tooltip.top="'Switch Data'"
                            class="btn btn-outline-info btn-wave waves-effect waves-light mt-6"
                        >
                            <i class="fa fa-arrows-h"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6" v-if="account.name">
                <div class="card" v-if="type == 'jurnal'">
                    <div class="card-body">
                        <div class="header-section">
                            <div
                                class="d-flex justify-content-between align-items-center"
                            >
                                <h5 class="card-title">JURNAL FAKTURCO</h5>
                            </div>
                            <div
                                class="d-flex justify-content-between align-items-center"
                            >
                                <span>Saldo</span>
                                <span>Rp {{ account.saldo }}</span>
                            </div>
                            <div class="warning-text">
                                {{ formatNumber(totalRows) }} data belum cocok
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card" v-else>
                    <div class="card-body">
                        <div class="header-section">
                            <div
                                class="d-flex justify-content-between align-items-center"
                            >
                                <h5 class="card-title">REKENING BANK</h5>
                            </div>
                            <div
                                class="d-flex justify-content-between align-items-center"
                            >
                                <span>Saldo</span>
                                <span>Rp {{ account.end_balance }}</span>
                            </div>
                            <div class="warning-text">
                                {{ formatNumber(totalRows) }} data belum cocok
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6" v-if="account.name">
                <div class="card" v-if="type == 'jurnal'">
                    <div class="card-body">
                        <div class="header-section">
                            <div
                                class="d-flex justify-content-between align-items-center"
                            >
                                <h5 class="card-title">REKENING BANK</h5>
                            </div>
                            <div
                                class="d-flex justify-content-between align-items-center"
                            >
                                <span>Saldo</span>
                                <span>Rp {{ account.end_balance }}</span>
                            </div>
                            <div class="warning-text"></div>
                        </div>
                    </div>
                </div>
                <div class="card" v-else>
                    <div class="card-body">
                        <div class="header-section">
                            <div
                                class="d-flex justify-content-between align-items-center"
                            >
                                <h5 class="card-title">JURNAL FAKTURCO</h5>
                            </div>
                            <div
                                class="d-flex justify-content-between align-items-center"
                            >
                                <span>Saldo</span>
                                <span>Rp {{ account.saldo }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="row"
            v-if="account.name && type == 'jurnal'"
            v-for="(item, index) in transactions"
            :key="index"
        >
            <div class="col-md-6 transaction-row">
                <div class="d-flex justify-content-between">
                    <div>{{ item.date }}</div>
                    <div>
                        ({{ item.type == "debit" ? "Dr" : "Cr" }})
                        {{ formatNumber(item.amount) }}
                    </div>
                </div>
                <div>
                    {{ item.name }} -
                    {{
                        item.customer.name != ""
                            ? item.customer.name
                            : item.supplier.name
                    }}
                </div>
                <a
                    href="javascript:void(0);"
                    style="text-decoration: none"
                    @click="
                        $goTo({
                            name: item.transaction.route,
                            params: {
                                id: item.transaction.id,
                            },
                        })
                    "
                    class="text-muted"
                    >{{ item.ref_no }}</a
                >
            </div>

            <div class="col-md-6 mb-4 mt-4" v-if="!item.status">
                <div class="card">
                    <div
                        class="card-body text-center d-flex justify-content-center"
                    >
                        <button
                            v-if="import_file"
                            type="button"
                            @click="checkListForJurnal(item)"
                            :disabled="import_file && !item.status"
                            v-tooltip.top="'Ada di Rekening Koran'"
                            class="me-2"
                            :class="
                                import_file && !item.status
                                    ? 'btn btn-dark'
                                    : 'btn btn-info'
                            "
                        >
                            <i class="fa fa-check me-1"></i>Cocok
                        </button>
                        <button
                            v-else
                            type="button"
                            @click="checkList(item)"
                            :disabled="import_file && !item.status"
                            v-tooltip.top="'Ada di Rekening Koran'"
                            class="me-2"
                            :class="
                                import_file && !item.status
                                    ? 'btn btn-dark'
                                    : 'btn btn-info'
                            "
                        >
                            <i class="fa fa-check me-1"></i>Cocok
                        </button>
                        <button
                            v-if="import_file"
                            type="button"
                            @click="searchMutasi(item, index)"
                            v-tooltip.top="'Cari Mutasi Bank'"
                            class="btn btn-info"
                        >
                            <i class="fa fa-search me-1"></i>Cari
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 transaction-row" v-else>
                <div class="d-flex justify-content-between">
                    <div>{{ item.mutation[0].date }}</div>
                    <div>
                        ({{ item.mutation[0].type == "debit" ? "Dr" : "Cr" }})
                        {{ formatNumber(item.mutation[0].amount) }}
                    </div>
                </div>
                <div>
                    {{ item.mutation[0].note }}
                </div>
                <div class="d-flex justify-content-center text-center mt-2">
                    <button
                        type="button"
                        @click="checkListForJurnal(item)"
                        v-tooltip.top="'Ada di Rekening Koran'"
                        class="btn btn-info me-2"
                    >
                        <i class="fa fa-check me-1"></i>Cocok
                    </button>
                    <button
                        type="button"
                        v-tooltip.top="'Cari Mutasi Bank'"
                        @click="searchMutasi(item, index)"
                        class="btn btn-info"
                    >
                        <i class="fa fa-search me-1"></i>Cari
                    </button>
                </div>
            </div>
        </div>

        <div
            class="row"
            v-if="account.name && type == 'mutasi' && import_file"
            v-for="(item, index) in transactions"
            :key="index"
        >
            <div class="col-md-6 transaction-row">
                <div class="d-flex justify-content-between">
                    <div>{{ item.date }}</div>
                    <div>
                        ({{ item.type == "debit" ? "Dr" : "Cr" }})
                        {{ formatNumber(item.amount) }}
                    </div>
                </div>
                <div>
                    {{ item.note }}
                </div>
                <div class="d-flex justify-content-end">
                    <a
                        href="javascript:void(0);"
                        @click="removeMutasi(item.id)"
                        class="btn btn-sm btn-danger"
                    >
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-6 mb-4 mt-4" v-if="!item.status">
                <div class="card">
                    <div
                        class="card-body text-center d-flex justify-content-center"
                    >
                        <button
                            type="button"
                            @click="checkListForAccount(item)"
                            :disabled="import_file && !item.status"
                            v-tooltip.top="'Ada di Rekening Koran'"
                            class="me-2"
                            :class="
                                import_file && !item.status
                                    ? 'btn btn-dark'
                                    : 'btn btn-info'
                            "
                        >
                            <i class="fa fa-check me-1"></i>Cocok
                        </button>
                        <button
                            type="button"
                            v-tooltip.top="'Cari Jurnal'"
                            class="btn btn-info me-2"
                            @click="searchJurnal(item, index)"
                        >
                            <i class="fa fa-search me-1"></i>Cari
                        </button>
                        <button
                            type="button"
                            v-tooltip.top="'Buat Jurnal'"
                            @click="createNew(item, index)"
                            class="btn btn-info"
                        >
                            <i class="fa fa-plus me-1"></i>Buat
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 transaction-row" v-else>
                <div class="d-flex justify-content-between">
                    <div>{{ item.account[0].date }}</div>
                    <div>
                        ({{ item.account[0].type == "debit" ? "Dr" : "Cr" }})
                        {{ formatNumber(item.account[0].amount) }}
                    </div>
                </div>
                <div>
                    {{ item.account[0].name }} -
                    {{
                        item.account[0].customer.name != ""
                            ? item.account[0].customer.name
                            : item.account[0].supplier.name
                    }}
                </div>
                <a
                    href="javascript:void(0);"
                    style="text-decoration: none"
                    @click="
                        $goTo({
                            name: item.account[0].transaction.route,
                            params: {
                                id: item.account[0].transaction.id,
                            },
                        })
                    "
                    class="text-muted"
                    >{{ item.account[0].ref_no }}</a
                >
                <div class="d-flex justify-content-center text-center mt-2">
                    <button
                        type="button"
                        @click="checkListForAccount(item)"
                        v-tooltip.top="'Ada di Rekening Koran'"
                        class="btn btn-info me-2"
                    >
                        <i class="fa fa-check me-1"></i>Cocok
                    </button>
                    <button
                        type="button"
                        v-tooltip.top="'Cari Mutasi Bank'"
                        @click="searchJurnal(item, index)"
                        class="btn btn-info"
                    >
                        <i class="fa fa-search me-1"></i>Cari
                    </button>
                </div>
            </div>
        </div>

        <div class="row" v-if="account.name && totalRows > transactions.length">
            <div class="col-12 d-flex justify-content-center">
                <button
                    class="btn btn-primary"
                    type="button"
                    :disabled="loader.data"
                    @click="nextPage()"
                >
                    Lebih Banyak
                </button>
            </div>
        </div>
    </div>
    <!-- End List Data -->

    <!-- Import Data -->
    <Dialog
        v-model:visible="modal.import"
        modal
        header=""
        :style="{ width: '60vh' }"
    >
        <div class="card-body ps-5 pe-5 pt-2 pb-5 rectangle3">
            <div class="row gy-3">
                <div class="col-xl-12 d-flex justify-content-center mt-3 mb-3">
                    <FileUpload
                        mode="basic"
                        v-model="modal.file"
                        v-tooltip="'Upload File Disini'"
                        @select="onFileSelected"
                        accept=".csv"
                        :maxFileSize="1000000"
                    />
                </div>
                <!-- End Code Form -->

                <div
                    class="col-xl-12 d-grid mt-4 d-flex justify-content-center"
                >
                    <button
                        type="button"
                        @click="importData"
                        v-tooltip="
                            'Sebelum Import Data, Pastikan File Telah di unggah'
                        "
                        :disabled="loader.submit"
                        class="btn btn-primary label-btn label-end"
                    >
                        {{ loader.submit ? "Mohon Tunggu...." : "Import Data" }}
                        <i class="ti ti-upload label-btn-icon ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </Dialog>
    <!-- End Import Data -->

    <!-- Nota -->
    <Dialog
        v-model:visible="nota.modal"
        header="Jurnal Fakturco"
        :style="{ width: '70rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <div class="row p-2">
            <div class="col-12 d-flex justify-content-start">
                <div>
                    <label class="form-label">Tanggal </label>
                    <div class="btn-group btn-group2 w-100" role="group">
                        <VueCtkDateTimePicker
                            label="Filter Tanggal"
                            locale="Asia/Jakarta"
                            class="form-control"
                            v-model="nota.filter.date"
                            @validate="filterDateNota"
                            :range="true"
                        />
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <DataTable
                        :value="nota.items"
                        :paginator="true"
                        :rows="nota.limit"
                        :rowsPerPageOptions="[20, 50, 100]"
                        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        :lazy="true"
                        :totalRecords="nota.totalRows"
                        @page="onPageChangeNota($event)"
                        class="table"
                        :loading="nota.loader"
                        responsiveLayout="scroll"
                        sortField="dynamicSortField"
                        currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                    >
                        <Column field="date" header="Tanggal"></Column>
                        <Column field="ref_no" header="Sumber">
                            <template #body="{ data }">
                                <a
                                    href="javascript:void(0);"
                                    style="text-decoration: none"
                                    @click="
                                        $goTo({
                                            name: data.transaction.route,
                                            params: {
                                                id: data.transaction.id,
                                            },
                                        })
                                    "
                                    class="text-muted"
                                    >{{ data.ref_no }}</a
                                >
                            </template>
                        </Column>
                        <Column header="Nominal">
                            <template #body="{ data }">
                                {{ data.type == "credit" ? "Cr" : "Dr" }}
                                {{ formatNumber(data.amount) }}
                            </template>
                        </Column>
                        <Column header="Keterangan">
                            <template #body="{ data }">
                                {{ data.name }} -
                                {{
                                    data.customer.name != ""
                                        ? data.customer.name
                                        : data.supplier.name
                                }}
                            </template>
                        </Column>

                        <Column field="action" header="Aksi">
                            <template #body="slotProps">
                                <button
                                    class="btn btn-sm btn-success"
                                    type="button"
                                    @click="
                                        selectedNota(
                                            slotProps.data,
                                            slotProps.index
                                        )
                                    "
                                >
                                    <i class="fa fa-check-circle"></i>
                                </button>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <div class="me-3">
                    <label class="form-label">Nilai Di Cocokan</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="nota.amount"
                            disabled
                            class="form-control"
                        />
                    </div>
                </div>
                <div>
                    <label class="form-label">Nilai Terpilih</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="nota.choose_amount"
                            disabled
                            class="form-control"
                        />
                    </div>
                </div>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button
                    type="button"
                    class="btn btn-info"
                    @click="pencocokanJurnal()"
                    :disabled="nota.amount != nota.choose_amount"
                >
                    Pilih
                </button>
            </div>
        </div>
    </Dialog>
    <!-- End Nota -->

    <!-- Mutasi -->
    <Dialog
        v-model:visible="mutasi.modal"
        header="Mutasi Rekening"
        :style="{ width: '70rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <div class="row p-2">
            <div class="col-12 d-flex justify-content-start">
                <div class="me-3">
                    <label class="form-label">Cari</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="mutasi.filter.name"
                            @keyup="getMutasi()"
                            class="form-control"
                            placeholder="Cari...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
                <div>
                    <label class="form-label">Tanggal </label>
                    <div class="btn-group btn-group2 w-100" role="group">
                        <VueCtkDateTimePicker
                            label="Filter Tanggal"
                            locale="Asia/Jakarta"
                            class="form-control"
                            v-model="mutasi.filter.date"
                            @validate="filterDateNota"
                            :range="true"
                        />
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <DataTable
                        :value="mutasi.items"
                        :paginator="true"
                        :rows="mutasi.limit"
                        :rowsPerPageOptions="[20, 50, 100]"
                        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        :lazy="true"
                        :totalRecords="mutasi.totalRows"
                        @page="onPageChangeMutasi($event)"
                        class="table"
                        :loading="mutasi.loader"
                        responsiveLayout="scroll"
                        sortField="dynamicSortField"
                        currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                    >
                        <Column field="date" header="Tanggal"></Column>
                        <Column field="ref_no" header="Sumber"> </Column>
                        <Column header="Nominal">
                            <template #body="{ data }">
                                {{ data.type == "credit" ? "Cr" : "Dr" }}
                                {{ formatNumber(data.amount) }}
                            </template>
                        </Column>
                        <Column header="Keterangan">
                            <template #body="{ data }">
                                {{ data.note }}
                            </template>
                        </Column>

                        <Column field="action" header="Aksi">
                            <template #body="slotProps">
                                <button
                                    class="btn btn-sm btn-success"
                                    type="button"
                                    @click="
                                        selectedMutasi(
                                            slotProps.data,
                                            slotProps.index
                                        )
                                    "
                                >
                                    <i class="fa fa-check-circle"></i>
                                </button>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <div class="me-3">
                    <label class="form-label">Nilai Di Cocokan</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="mutasi.amount"
                            disabled
                            class="form-control"
                        />
                    </div>
                </div>
                <div>
                    <label class="form-label">Nilai Terpilih</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="mutasi.choose_amount"
                            disabled
                            class="form-control"
                        />
                    </div>
                </div>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button
                    type="button"
                    class="btn btn-info"
                    @click="pencocokanMutasi()"
                    :disabled="mutasi.amount != mutasi.choose_amount"
                >
                    Pilih
                </button>
            </div>
        </div>
    </Dialog>
    <!-- End Mutasi -->

    <!-- Create New -->
    <Dialog
        v-model:visible="new_data.modal"
        class="filter-data"
        :header="'Buat Transaksi Baru'"
        :style="{ width: '35rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <Form @submit="createNewTransaction()" ref="ValidationNewTransaction">
            <div class="row p-3">
                <div
                    class="col-lg-6 mb-2"
                    v-if="new_data.detail.type == 'credit'"
                >
                    <label for="user-date" class="form-label"
                        >Menjadi Transaksi</label
                    >
                    <Field
                        :rules="{
                            required: true,
                        }"
                        v-slot="{ errors }"
                        v-model="new_data.type"
                        :name="'Menjadi Transaksi'"
                    >
                        <Dropdown
                            v-model="new_data.type"
                            :options="[
                                {
                                    name: 'Pembayaran',
                                    value: 'expense',
                                },
                                {
                                    name: 'Transfer Dana',
                                    value: 'transfer',
                                },
                            ]"
                            optionLabel="name"
                            optionValue="value"
                            placeholder="Pilih"
                            style="width: 100%"
                        />
                        <div class="fs-sm text-danger">
                            {{ errors[0] }}
                        </div>
                    </Field>
                </div>

                <div
                    class="col-lg-6 mb-2"
                    v-if="new_data.detail.type == 'debit'"
                >
                    <label for="user-date" class="form-label"
                        >Menjadi Transaksi</label
                    >
                    <Field
                        :rules="{
                            required: true,
                        }"
                        v-slot="{ errors }"
                        v-model="new_data.type"
                        :name="'Menjadi Transaksi'"
                    >
                        <Dropdown
                            v-model="new_data.type"
                            :options="[
                                {
                                    name: 'Penerimaan',
                                    value: 'cash_int',
                                },
                                {
                                    name: 'Transfer Bank',
                                    value: 'transfer',
                                },
                            ]"
                            optionLabel="name"
                            optionValue="value"
                            placeholder="Pilih"
                            style="width: 100%"
                        />
                        <div class="fs-sm text-danger">
                            {{ errors[0] }}
                        </div>
                    </Field>
                </div>

                <div class="col-lg-6 mb-2">
                    <label for="user-ref" class="form-label"
                        >Tanggal Transfer</label
                    >
                    <input
                        type="date"
                        style="width: 100%"
                        v-model="new_data.date"
                        class="form-control"
                    />
                    <div class="fs-sm text-secondary">
                        Opsional, ( Di isi otomatis apabila di kosongkan)
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="user-date" class="form-label"
                        >Akun Perkiraan</label
                    >
                    <Field
                        :rules="{
                            required: true,
                        }"
                        v-slot="{ errors }"
                        v-model="new_data.account"
                        :name="'Akun Perkiraan'"
                    >
                        <Multiselect
                            v-model="new_data.account"
                            :options="listaccounts"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            :searchable="true"
                            :internal-search="false"
                            :options-limit="50"
                            placeholder="Pilih Akun"
                            open-direction="bottom"
                            label="name"
                            id="id"
                            track-by="name"
                            @search-change="getAccountData"
                        ></Multiselect>
                        <div class="fs-sm text-danger">
                            {{ errors[0] }}
                        </div>
                    </Field>
                </div>

                <div class="col-12">
                    <Divider />
                </div>
                <div class="col-12">
                    <label for="regular-form-1" class="form-label"
                        >Keterangan
                    </label>
                    <textarea
                        v-model="new_data.note"
                        class="form-control"
                    ></textarea>
                </div>
            </div>
        </Form>
        <template #footer>
            <button
                type="button"
                @click="createNewTransaction()"
                class="btn btn-outline-info btn-wave waves-effect waves-light"
            >
                Lanjut
            </button>
        </template>
    </Dialog>
    <!-- End Create New -->
</template>

<script>
import Swal from "sweetalert2";
import NProgress from "nprogress";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    components: {},
    data() {
        return {
            data: new FormData(),
            editmode: false,
            transactions: [],
            modal: {
                import: false,
                file: null,
                files: null,
            },
            listaccounts: [],
            new_data: {
                modal: false,
                detail: {},
                index: null,
                id: null,
                type: "",
                date: "",
                amount: 0,
                note: "",
                account: {
                    id: "",
                    name: "",
                },
            },
            nota: {
                modal: false,
                id: null,
                index: null,
                items: [],
                totalRows: 0,
                page: 1,
                limit: 10,
                loader: false,
                selecteds: [],
                amount: 0,
                type: "",
                choose_amount: 0,
                filter: {
                    date: {
                        start: "",
                        end: "",
                    },
                    name: "",
                },
            },
            mutasi: {
                modal: false,
                id: null,
                index: null,
                items: [],
                totalRows: 0,
                page: 1,
                limit: 10,
                type: "",
                loader: false,
                selecteds: [],
                amount: 0,
                choose_amount: 0,
                filter: {
                    date: {
                        start: "",
                        end: "",
                    },
                    name: "",
                },
            },
            totalRows: 0,
            page: 1,
            limit: 10,
            accounts: [],
            import_file: false,
            type: "jurnal",
            account: {
                name: "",
                code: "",
                saldo: 0,
                end_balance: 0,
            },
            loader: {
                session: false,
                data: false,
                account: false,
                submit: false,
            },
            filter: {
                name: "",
                account: {
                    id: "",
                    name: "",
                },
                date: {
                    start: "",
                    end: "",
                },
            },
        };
    },
    computed: {},
    created() {},
    methods: {
        createNew(item, index) {
            this.new_data = {
                modal: false,
                detail: item,
                index: index,
                id: item.id,
                type: item.type == "credit" ? "expense" : "cash_int",
                date: item.date,
                amount: item.amount,
                note: "",
                account: {
                    id: "",
                    name: "",
                },
            };

            this.new_data.modal = true;
        },

        removeMutasi(id) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Data yang telah di hapus tidak dapat di kembalikan lagi kecuali di import ulang",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.delete(
                        "app/account/rekonsiliasi/action/remove-mutasi/" + id
                    )
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.getData();
                        })
                        .catch((err) => {
                            NProgress.done();
                            this.$handleErrorResponse(err);
                        });
                } else {
                    Swal.fire("Membatalkan Proses Rekonsiliasi");
                }
            });
        },

        createNewTransaction() {
            this.$refs.ValidationNewTransaction.validate().then((success) => {
                if (!success) {
                    this.$toast.add({
                        severity: "error",
                        summary: "Terjadi kesalahan",
                        detail: "Silahkan Check kembali form inputan anda",
                        life: 3000,
                    });
                } else {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.post(
                        `app/account/rekonsiliasi/action/create/${this.filter.account.id}/${this.new_data.id}`,
                        this.new_data
                    )
                        .then((response) => {
                            this.new_data.modal = false;
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.getData();
                        })
                        .catch((err) => {
                            NProgress.done();
                            this.$handleErrorResponse(err);
                        });
                }
            });
        },

        async getAccountData(query) {
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&only_parent=yes`
                );
                var data = response.data;
                this.listaccounts = data.accounts;
                this.loader.account = false;
            } catch (error) {
                console.log(error);
            }
        },

        filterDateNota() {
            var date = this.nota.filter.date;
            if (date != null) {
                this.nota.filter.date = {
                    start:
                        date.start != null ? date.start.substring(0, 10) : "",
                    end: date.end != null ? date.end.substring(0, 10) : "",
                };

                this.getNota();
            }
        },

        filterDateMutasi() {
            var date = this.mutasi.filter.date;
            if (date != null) {
                this.mutasi.filter.date = {
                    start:
                        date.start != null ? date.start.substring(0, 10) : "",
                    end: date.end != null ? date.end.substring(0, 10) : "",
                };

                this.getMutasi();
            }
        },

        selectedNota(data, index) {
            var id = false;
            id = this.nota.selecteds.filter((item) => {
                if (item.id == data.id) {
                    return true;
                }
            });

            if (id == false) {
                this.nota.selecteds.push(data);
                var type = this.nota.type;

                if (item.type == type) {
                    this.nota.choose_amount += data.amount;
                } else {
                    this.nota.choose_amount -= data.amount;
                }
            }

            this.nota.items.splice(index, 1);
        },

        selectedMutasi(data, index) {
            var id = false;
            id = this.mutasi.selecteds.filter((item) => {
                if (item.id == data.id) {
                    return true;
                }
            });

            if (id == false) {
                this.mutasi.selecteds.push(data);

                var type = this.mutasi.type;

                if (item.type == type) {
                    this.mutasi.choose_amount += data.amount;
                } else {
                    this.mutasi.choose_amount -= data.amount;
                }
            }

            this.mutasi.items.splice(index, 1);
        },

        onPageChangeNota(e) {
            this.nota.limit = e.rows;
            this.nota.page = e.page += 1;
            this.getNota(this.nota.page);
        },

        onPageChangeMutasi(e) {
            this.mutasi.limit = e.rows;
            this.mutasi.page = e.page += 1;
            this.getMutasi(this.mutasi.page);
        },

        searchJurnal(item, index) {
            this.nota.filter = {
                date: {
                    start: item.daterange[0],
                    end: item.daterange[1],
                },
                name: "",
            };

            this.nota.index = index;
            this.nota.id = item.id;
            this.nota.amount = item.amount;
            this.nota.choose_amount = 0;
            this.nota.type = item.type;
            this.nota.selecteds = [];
            this.nota.modal = true;
            this.getNota();
        },

        searchMutasi(item, index) {
            this.mutasi.filter = {
                date: {
                    start: item.daterange[0],
                    end: item.daterange[1],
                },
                name: "",
            };

            this.mutasi.index = index;
            this.mutasi.id = item.id;
            this.mutasi.amount = item.amount;
            this.mutasi.choose_amount = 0;
            this.mutasi.type = item.type;
            this.mutasi.selecteds = [];
            this.mutasi.modal = true;
            this.getMutasi();
        },

        pencocokanJurnal() {
            this.transactions[this.nota.index].account = this.nota.selecteds;
            this.transactions[this.nota.index].status = true;
            this.nota.modal = false;
        },

        pencocokanMutasi() {
            this.transactions[this.mutasi.index].mutation =
                this.mutasi.selecteds;
            this.transactions[this.mutasi.index].status = true;
            this.mutasi.modal = false;
        },

        async getNota(page = 1) {
            try {
                this.nota.loader = true;
                const response = await ApiData.get(
                    `app/account/rekonsiliasi/data/nota/${this.nota.id}?limit=${this.nota.limit}&page=${page}&account=${this.filter.account.id}&start=${this.nota.filter.date.start}&end=${this.nota.filter.date.end}&after_rekonsiliasi=no`
                );
                var data = response.data;
                this.nota.items = data.items;
                this.nota.totalRows = data.totalRows;
                this.nota.loader = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getMutasi(page = 1) {
            try {
                this.mutasi.loader = true;
                const response = await ApiData.get(
                    `app/account/rekonsiliasi/data/mutasi/${this.mutasi.id}?limit=${this.mutasi.limit}&page=${page}&account=${this.filter.account.id}&start=${this.mutasi.filter.date.start}&end=${this.mutasi.filter.date.end}&name=${this.mutasi.filter.name}`
                );
                var data = response.data;
                this.mutasi.items = data.items;
                this.mutasi.totalRows = data.totalRows;
                this.mutasi.loader = false;
            } catch (error) {
                console.log(error);
            }
        },

        importData() {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);
            this.data.append("file", this.modal.files);
            ApiData.post(
                "app/account/rekonsiliasi/import/" + this.filter.account.id,
                this.data
            )
                .then((response) => {
                    this.loader.submit = false;
                    this.$handleSuccessResponse(response.data.message);
                    this.modal.import = false;
                    NProgress.done();
                    this.getData();
                })
                .catch((error) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(error);
                });
        },

        async onFileSelected(e) {
            if (e.files[0] != undefined) {
                this.modal.files = e.files[0];
            } else {
                this.modal.files = null;
            }
        },

        nextPage() {
            this.page = this.page + 1;
            this.getData(this.page, this.type);
        },

        async getData(page = 1, type = "") {
            if (
                this.filter.account.id != "" &&
                this.filter.account.id != null
            ) {
                this.loader.data = true;
                this.page = page;
                type = type == "" ? this.type : type;

                try {
                    const response = await ApiData.get(
                        `app/account/rekonsiliasi?limit=${this.limit}&page=${this.page}&account=${this.filter.account.id}&start=${this.filter.date.start}&end=${this.filter.date.end}&after_rekonsiliasi=no&type=${type}`
                    );
                    var data = response.data;

                    this.transactions =
                        this.page > 1
                            ? [...this.transactions, ...data.transactions] // Append data baru
                            : data.transactions; // Replace jika page == 1

                    this.totalRows = data.totalRows;
                    this.import_file = data.account.import;
                    this.type =
                        type == "jurnal" || type == "mutasi" ? type : "jurnal";
                    if (!this.import_file) {
                        this.type = "jurnal";
                    }

                    this.account = data.account;
                    this.loader.data = false;
                } catch (error) {
                    console.log(error);
                }
            }
        },

        filterDate() {
            var date = this.filter.date;
            if (date != null) {
                this.filter.date = {
                    start:
                        date.start != null ? date.start.substring(0, 10) : "",
                    end: date.end != null ? date.end.substring(0, 10) : "",
                };

                this.getData();
            }
        },

        checkList(data) {
            if (!this.loader.session) {
                Swal.fire({
                    title: "Apakah Anda Yakin ?",
                    text: "Transaksi yang telah di klaim ada di rekening koran tidak dapat di edit kembali",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ok",
                }).then((result) => {
                    if (result.isConfirmed) {
                        NProgress.start();
                        NProgress.set(0.1);
                        ApiData.post(
                            "app/account/rekonsiliasi/action/basic/" + data.id
                        )
                            .then((response) => {
                                this.$handleSuccessResponse(
                                    response.data.message
                                );
                                NProgress.done();
                                this.loader.session = true;
                                this.getData();
                            })
                            .catch((err) => {
                                NProgress.done();
                                this.$handleErrorResponse(err);
                            });
                    } else {
                        Swal.fire("Membatalkan Proses Rekonsiliasi");
                    }
                });
            } else {
                NProgress.start();
                NProgress.set(0.1);
                ApiData.post("app/account/rekonsiliasi/action/basic/" + data.id)
                    .then((response) => {
                        this.$handleSuccessResponse(response.data.message);
                        NProgress.done();
                        this.loader.session = true;
                        this.getData();
                    })
                    .catch((err) => {
                        NProgress.done();
                        this.$handleErrorResponse(err);
                    });
            }
        },

        checkListForJurnal(data) {
            if (!this.loader.session) {
                Swal.fire({
                    title: "Apakah Anda Yakin ?",
                    text: "Transaksi yang telah di klaim ada di rekening koran tidak dapat di edit kembali",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ok",
                }).then((result) => {
                    if (result.isConfirmed) {
                        NProgress.start();
                        NProgress.set(0.1);
                        ApiData.post(
                            "app/account/rekonsiliasi/action/jurnal/" + data.id,
                            data
                        )
                            .then((response) => {
                                this.$handleSuccessResponse(
                                    response.data.message
                                );
                                NProgress.done();
                                this.loader.session = true;
                                this.getData();
                            })
                            .catch((err) => {
                                NProgress.done();
                                this.$handleErrorResponse(err);
                            });
                    } else {
                        Swal.fire("Membatalkan Proses Rekonsiliasi");
                    }
                });
            } else {
                NProgress.start();
                NProgress.set(0.1);
                ApiData.post(
                    "app/account/rekonsiliasi/action/jurnal/" + data.id,
                    data
                )
                    .then((response) => {
                        this.$handleSuccessResponse(response.data.message);
                        NProgress.done();
                        this.loader.session = true;
                        this.getData();
                    })
                    .catch((err) => {
                        NProgress.done();
                        this.$handleErrorResponse(err);
                    });
            }
        },

        checkListForAccount(data) {
            if (!this.loader.session) {
                Swal.fire({
                    title: "Apakah Anda Yakin ?",
                    text: "Transaksi yang telah di klaim ada di rekening koran tidak dapat di edit kembali",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ok",
                }).then((result) => {
                    if (result.isConfirmed) {
                        NProgress.start();
                        NProgress.set(0.1);
                        ApiData.post(
                            "app/account/rekonsiliasi/action/mutasi/" + data.id,
                            data
                        )
                            .then((response) => {
                                this.$handleSuccessResponse(
                                    response.data.message
                                );
                                NProgress.done();
                                this.loader.session = true;
                                this.getData();
                            })
                            .catch((err) => {
                                NProgress.done();
                                this.$handleErrorResponse(err);
                            });
                    } else {
                        Swal.fire("Membatalkan Proses Rekonsiliasi");
                    }
                });
            } else {
                NProgress.start();
                NProgress.set(0.1);
                ApiData.post(
                    "app/account/rekonsiliasi/action/mutasi/" + data.id,
                    data
                )
                    .then((response) => {
                        this.$handleSuccessResponse(response.data.message);
                        NProgress.done();
                        this.loader.session = true;
                        this.getData();
                    })
                    .catch((err) => {
                        NProgress.done();
                        this.$handleErrorResponse(err);
                    });
            }
        },

        searchData() {
            this.doSearch(this);
        },

        doSearch: _.debounce((rootInstance) => {
            rootInstance.getData();
        }, 300),

        onPageChange(e) {
            this.limit = e.rows;
            this.page = e.page += 1;
            this.getData(this.page);
        },

        formatNumber(number) {
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
        },

        removeFilter(type) {
            if (type == "date") {
                this.filter.date = {
                    start: "",
                    end: "",
                };
            }

            if (type == "account") {
                this.filter.account = {
                    id: "",
                    name: "",
                };
                this.transactions = [];
                this.totalRows = 0;
                this.page = 1;

                this.account = {
                    name: "",
                    code: "",
                    saldo: 0,
                };
            }

            this.getData();
        },

        async getAccount(query) {
            this.loader.account = true;
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&price=bank_cash&only_parent=yes`
                );
                var data = response.data;
                this.accounts = data.accounts;
                this.loader.account = false;
            } catch (error) {
                console.log(error);
            }
        },
    },
    mounted: function () {
        this.getAccount("");
        this.getAccountData("");
    },
    watch: {
        "filter.date": function (newDate, oldDate) {
            if (newDate === null) {
                this.filter.date = {
                    start: "",
                    end: "",
                };
                this.getData();
            }
        },
    },
};
</script>
<style>
.transaction-row {
    background-color: #e3f2fd;
    margin-bottom: 10px;
    padding: 25px;
    border-radius: 5px;
}
.header-section {
    border-bottom: 3px solid #dee2e6;
    padding-bottom: 10px;
    margin-bottom: 20px;
}
.warning-text {
    color: red;
    font-size: 0.9em;
}
.bank-button {
    background-color: #4a7a8c;
    color: white;
    border: none;
    padding: 5px 15px;
    border-radius: 5px;
}
</style>
