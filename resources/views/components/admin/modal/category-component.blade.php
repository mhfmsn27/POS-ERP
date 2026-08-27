<div class="modal fade" id="createCategory" tabindex="-1" role="dialog" aria-labelledby="add-category" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-xl " role="document">
        <form method="POST" id="addCategoryStore" class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header header-modal ">
                <h5 class="modal-title" id="">Tambahkan Kategori Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <i data-feather="x"></i> </button>
            </div>
            <div class="modal-body">
                <div class="form form-horizontal">
                    <div class="form-body p-2">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Nama Kategori *</label>
                            </div>
                            <div class="col-md-8 form-group">
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" id="name" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Kategori Induk</label>
                            </div>
                            <div class="col-md-8 form-group">
                                <select class="choices form-control" name="parent_id">
                                    <option value="">{{ __('category.choose_category') }}</option>
                                    @foreach ($category as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                <small>Kosongkan Kategori Induk Apabila Anda berniat menambahkan kategori Induk dan bukan Sub Kategori</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">{{ __('general.close') }}</span>
                </button>
                <button type="submit" class="btn btn-primary ml-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Tambahkan Kategori</span>
                </button>
            </div>
        </form>
    </div>
</div>