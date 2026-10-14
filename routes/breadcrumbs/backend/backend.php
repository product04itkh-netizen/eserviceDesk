<?php

Breadcrumbs::for('admin.dashboard', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('admin.dashboard'));
});

Breadcrumbs::for('admin.customer.index', function ($trail) {
    $trail->push(__('strings.backend.customer.title'), route('admin.customer.index'));
});

Breadcrumbs::for('admin.vendor.index', function ($trail) {
    $trail->push(__('strings.backend.vendor.title'), route('admin.vendor.index'));
});

Breadcrumbs::for('admin.vendor.create', function ($trail) {
    $trail->push(__('strings.backend.vendor.title'), route('admin.vendor.index'));
    $trail->push(__('strings.backend.newvendor.title'), route('admin.vendor.create'));
});

Breadcrumbs::for('admin.vendor.edit', function ($trail) {
    $trail->push(__('strings.backend.vendor.title'), route('admin.vendor.index'));
    $trail->push(__('strings.backend.editvendor.title'), route('admin.vendor.index'));
});


Breadcrumbs::for('admin.customer.create', function ($trail) {
    $trail->push(__('strings.backend.customer.title'), route('admin.customer.index'));
    $trail->push(__('strings.backend.newcustomer.title'), route('admin.customer.create'));
});

Breadcrumbs::for('admin.customer.edit', function ($trail) {
    $trail->push(__('strings.backend.customer.title'), route('admin.customer.index'));
    $trail->push(__('strings.backend.editcustomer.title'), route('admin.customer.index'));
});


Breadcrumbs::for('admin.problem.index', function ($trail) {
    $trail->push(__('strings.backend.problem.title'), route('admin.problem.index'));
});

Breadcrumbs::for('admin.problem.create', function ($trail) {
    $trail->push(__('strings.backend.problem.title'), route('admin.problem.index'));
    $trail->push(__('strings.backend.newproblem.title'), route('admin.problem.index'));
});

Breadcrumbs::for('admin.problem.edit', function ($trail) {
    $trail->push(__('strings.backend.problem.title'), route('admin.problem.index'));
    $trail->push(__('strings.backend.editproblem.title'), route('admin.problem.index'));
});

Breadcrumbs::for('admin.service.index', function ($trail) {
    $trail->push(__('strings.backend.service.title'), route('admin.service.index'));

});


Breadcrumbs::for('admin.service.rma_paid', function ($trail) {
    $trail->push(__('strings.backend.service.title'), route('admin.service.index'));
    $trail->push(__('strings.backend.rma_paid.title'), route('admin.service.rma_paid'));
});

Breadcrumbs::for('admin.selling.report', function ($trail) {
    $trail->push(__('strings.backend.selling_report.title'), route('admin.selling.report'));
});

Breadcrumbs::for('admin.service.transfer_clearance', function ($trail) {
    $trail->push(__('strings.backend.service.title'), route('admin.service.index'));
    $trail->push(__('strings.backend.transfer_clearance.title'), route('admin.service.transfer_clearance'));
});

Breadcrumbs::for('admin.service.cash_transfer', function ($trail) {
    $trail->push(__('strings.backend.service.title'), route('admin.service.index'));
    $trail->push(__('strings.backend.cash_transfer.title'), route('admin.service.cash_transfer'));
});

Breadcrumbs::for('admin.service.calling', function ($trail) {
    $trail->push(__('strings.backend.service.title'), route('admin.service.index'));
    $trail->push(__('strings.backend.calling.title'), route('admin.service.calling'));
});

Breadcrumbs::for('admin.service.shipping', function ($trail) {
    $trail->push(__('strings.backend.service.title'), route('admin.service.index'));
    $trail->push(__('strings.backend.shipping.title'), route('admin.service.shipping',['id'=>0]));
});

Breadcrumbs::for('admin.service.transfer', function ($trail) {
    $trail->push(__('strings.backend.service.title'), route('admin.service.index'));
    $trail->push(__('strings.backend.transfer.title'), route('admin.service.transfer',['id'=>0]));
});

Breadcrumbs::for('admin.service.transfer.edit', function ($trail) {
    $trail->push(__('strings.backend.service.title'), route('admin.service.index'));
    $trail->push(__('strings.backend.transfer.title'), route('admin.service.transfer',['id'=>0]));
});

Breadcrumbs::for('admin.service.shipping.edit', function ($trail) {
    $trail->push(__('strings.backend.service.title'), route('admin.service.index'));
    $trail->push(__('strings.backend.editshipping.title'), route('admin.service.shipping',['id'=>0]));
});
Breadcrumbs::for('admin.service.create', function ($trail) {
    $trail->push(__('strings.backend.service.title'), route('admin.service.index'));
    $trail->push(__('strings.backend.newservice.title'), route('admin.service.create'));
});

Breadcrumbs::for('admin.service.edit', function ($trail) {
    $trail->push(__('strings.backend.service.title'), route('admin.service.index'));
    $trail->push(__('strings.backend.editservice.title'), route('admin.service.index'));
});

Breadcrumbs::for('admin.service.show', function ($trail) {
    $trail->push(__('strings.backend.service.title'), route('admin.service.index'));
    $trail->push(__('strings.backend.viewservice.title'), route('admin.service.index'));
});

Breadcrumbs::for('admin.category.index', function ($trail) {
    $trail->push(__('strings.backend.category.title'), route('admin.category.index'));
});

Breadcrumbs::for('admin.brand.index', function ($trail) {
    $trail->push(__('strings.backend.brand.title'), route('admin.brand.index'));
});


Breadcrumbs::for('admin.enginner_busy.index', function ($trail) {
    $trail->push(__('strings.backend.enginner_busy.title'), route('admin.enginner_busy.index'));
});


Breadcrumbs::for('admin.brand.create', function ($trail) {
    $trail->push(__('strings.backend.brand.title'), route('admin.brand.index'));
    $trail->push(__('strings.backend.newbrand.title'), route('admin.brand.create'));
});

Breadcrumbs::for('admin.brand.edit', function ($trail) {
    $trail->push(__('strings.backend.brand.title'), route('admin.brand.index'));
    $trail->push(__('strings.backend.editbrand.title'), route('admin.brand.index'));
});


Breadcrumbs::for('admin.rmacode.index', function ($trail) {
    $trail->push(__('strings.backend.rmacode.title'), route('admin.rmacode.index'));

});

Breadcrumbs::for('admin.rmacode.create', function ($trail) {
    $trail->push(__('strings.backend.rmacode.title'), route('admin.rmacode.index'));
    $trail->push(__('strings.backend.newrmacode.title'), route('admin.rmacode.create'));
});

Breadcrumbs::for('admin.rmacode.edit', function ($trail) {
    $trail->push(__('strings.backend.rmacode.title'), route('admin.rmacode.index'));
    $trail->push(__('strings.backend.editrmacode.title'), route('admin.rmacode.index'));
});

Breadcrumbs::for('admin.category.create', function ($trail) {
    $trail->push(__('strings.backend.category.title'), route('admin.category.index'));
    $trail->push(__('strings.backend.newcategory.title'), route('admin.category.create'));
});

Breadcrumbs::for('admin.category.edit', function ($trail) {
    $trail->push(__('strings.backend.category.title'), route('admin.category.index'));
    $trail->push(__('strings.backend.editcategory.title'), route('admin.category.index'));
});

Breadcrumbs::for('admin.item.index', function ($trail) {
    $trail->push(__('strings.backend.item.title'), route('admin.item.index'));
});

Breadcrumbs::for('admin.item.create', function ($trail) {
    $trail->push(__('strings.backend.item.title'), route('admin.item.index'));
    $trail->push(__('strings.backend.newitem.title'), route('admin.item.create'));
});

Breadcrumbs::for('admin.item.edit', function ($trail) {
    $trail->push(__('strings.backend.item.title'), route('admin.item.index'));
    $trail->push(__('strings.backend.edit.title'), route('admin.item.index'));
});

Breadcrumbs::for('admin.service.receipt', function ($trail) {
    $trail->push(__('strings.backend.servicereceipt.title'), route('admin.service.index'));
});

Breadcrumbs::for('admin.service.invoice', function ($trail) {
    $trail->push(__('strings.backend.serviceinvoice.title'), route('admin.service.index'));
});

Breadcrumbs::for('admin.stock.control', function ($trail) {
    $trail->push(__('strings.backend.stock.title'), '');
});


Breadcrumbs::for('admin.purchase.create', function ($trail) {
  $trail->push(__('strings.backend.rma.title'), route('admin.purchase.index'));
  $trail->push(__('strings.backend.newrma.title'), route('admin.purchase.create'));
});

Breadcrumbs::for('admin.create.rma', function ($trail) {
    $trail->push(__('strings.backend.rma.title'), route('admin.purchase.index'));
    $trail->push(__('strings.backend.newrma.title'), route('admin.purchase.create'));
});


Breadcrumbs::for('admin.service.report', function ($trail) {
    $trail->push(__('strings.backend.reportservice.title'), route('admin.service.report'));

});


Breadcrumbs::for('admin.agin.report', function ($trail) {
    $trail->push(__('strings.backend.report_agin.title'), route('admin.agin.report'));

});

Breadcrumbs::for('admin.rma.report', function ($trail) {
    $trail->push(__('strings.backend.report_rma.title'), route('admin.rma.report'));

});

Breadcrumbs::for('admin.shipping.report', function ($trail) {
    $trail->push(__('strings.backend.report_shipping.title'), route('admin.shipping.report'));

});

Breadcrumbs::for('admin.transfer.report', function ($trail) {
    $trail->push(__('strings.backend.report_transfer.title'), route('admin.transfer.report'));

});

Breadcrumbs::for('admin.enginner.report', function ($trail) {
    $trail->push(__('strings.backend.report_enginner.title'), route('admin.enginner.report'));

});

Breadcrumbs::for('admin.cash.report', function ($trail) {
    $trail->push(__('strings.backend.report_cash.title'), route('admin.cash.report'));

});

Breadcrumbs::for('admin.service.pdfreport', function ($trail) {
    $trail->push(__('strings.backend.reportservice.title'), route('admin.service.report'));

});

Breadcrumbs::for('admin.purchase.index', function ($trail) {
    $trail->push(__('strings.backend.purchase.title'), route('admin.purchase.index'));

});

Breadcrumbs::for('admin.purchase.edit', function ($trail) {
    $trail->push(__('strings.backend.purchase.title'), route('admin.purchase.index'));
  $trail->push(__('strings.backend.editpurchase.title'), route('admin.purchase.index'));
});


Breadcrumbs::for('admin.saleorder.index', function ($trail) {
    $trail->push(__('strings.backend.saleorder.title'), route('admin.saleorder.index'));
});

Breadcrumbs::for('admin.saleorder.create', function ($trail) {
    $trail->push(__('strings.backend.saleorder.title'), route('admin.saleorder.index'));
    $trail->push(__('strings.backend.newsaleorder.title'), route('admin.saleorder.index'));
});

Breadcrumbs::for('admin.saleorder.edit', function ($trail) {
    $trail->push(__('strings.backend.saleorder.title'), route('admin.saleorder.index'));
    $trail->push(__('strings.backend.editsaleorder.title'), route('admin.saleorder.index'));
});

Breadcrumbs::for('admin.saleorder.show', function ($trail) {
    $trail->push(__('strings.backend.saleinvoice.title'), route('admin.saleorder.index'));
});

Breadcrumbs::for('admin.stock.report_purchase', function ($trail) {
    $trail->push(__('strings.backend.reportpurchase.title'), route('admin.stock.report_purchase'));
});

Breadcrumbs::for('admin.stock.report_sale', function ($trail) {
    $trail->push(__('strings.backend.reportsale.title'), route('admin.stock.report_sale'));
});

require __DIR__.'/auth.php';
require __DIR__.'/log-viewer.php';
