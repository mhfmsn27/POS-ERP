"use strict";
$(function () {
    typetransaction();
    todaypayment(); 
});

var colors = ["#886ab5", "#1dc9b7", "#2196f3", "#fd3995", "#ffc241"];

function typetransaction() {
    $.ajax({
        url:
            domain + domainpath + "/pos-admin/shift-register/today-transaction",
        type: "GET",
        data: "",
        success: function (data) {
            console.log(data);

            var sell = data.sell.map(function (e) {
                return e.total;
            });

            var opening = data.cash.map(function (e) {
                return e.total;
            });

            var refund = data.return.map(function (e) {
                return e.total;
            });

            var expense = data.expense.map(function (e) {
                return e.total;
            });

            var pieChart = c3.generate({
                bindto: "#summaryTransaction",
                data: {
                    // iris data from R
                    columns: [
                        ["Penjualan", sell],
                        ["Cash di Tangan", opening],
                        ["Return Penjualan", refund],
                        ["Pengeluaran", expense],
                    ],
                    type: "pie", //,
                },
                color: {
                    pattern: colors,
                },
            });
        },

        cache: false,
        contentType: false,
        processData: false,
    });
}

function todaypayment() {
    $.ajax({
        url: domain + domainpath + "/pos-admin/shift-register/today-payment",
        type: "GET",
        data: "",
        success: function (data) {
            var cash = data.cash.map(function (e) {
                return e.total;
            });

            var bank = data.bank.map(function (e) {
                return e.total;
            });

            var other = data.other.map(function (e) {
                return e.total;
            });

            var donutChart = c3.generate({
                bindto: "#payment",
                data: {
                    // iris data from R
                    columns: [
                        ["Via Cash", cash],
                        ["Via Bank", bank],
                        ["Lainnya", other],
                    ],
                    type: "donut", //,
                },
                donut: {
                    title: "Pembayaran",
                },
                color: {
                    pattern: colors,
                },
            });
        },

        cache: false,
        contentType: false,
        processData: false,
    });
}

 
