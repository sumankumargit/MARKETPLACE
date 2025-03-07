/*! DataTables Bootstrap 5 integration
 * © SpryMedia Ltd - datatables.net/license
 */
!(function (n) {
  var o, r;
  "function" == typeof define && define.amd
    ? define(["jquery", "datatables.net"], function (t) {
        return n(t, window, document);
      })
    : "object" == typeof exports
    ? ((o = require("jquery")),
      (r = function (t, e) {
        e.fn.dataTable || require("datatables.net")(t, e);
      }),
      "undefined" == typeof window
        ? (module.exports = function (t, e) {
            return (
              (t = t || window), (e = e || o(t)), r(t, e), n(e, 0, t.document)
            );
          })
        : (r(window, o), (module.exports = n(o, window, window.document))))
    : n(jQuery, window, document);
})(function (d, t, e) {
  "use strict";
  var n = d.fn.dataTable;
  return (
    d.extend(!0, n.defaults, { renderer: "bootstrap" }),
    d.extend(!0, n.ext.classes, {
      container: "dt-container dt-bootstrap5",
      search: { input: "form-control form-control-sm" },
      length: { select: "form-select form-select-sm" },
      processing: { container: "dt-processing card" },
      layout: {
        row: "row mt-2 justify-content-between",
        cell: "d-md-flex justify-content-between align-items-center",
        tableCell: "col-12",
        start: "dt-layout-start col-md-auto me-auto",
        end: "dt-layout-end col-md-auto ms-auto",
        full: "dt-layout-full col-md",
      },
    }),
    (n.ext.renderer.pagingButton.bootstrap = function (t, e, n, o, r) {
      var a = ["dt-paging-button", "page-item"],
        o =
          (o && a.push("active"),
          r && a.push("disabled"),
          d("<li>").addClass(a.join(" ")));
      return {
        display: o,
        clicker: d("<button>", {
          class: "page-link",
          role: "link",
          type: "button",
        })
          .html(n)
          .appendTo(o),
      };
    }),
    (n.ext.renderer.pagingContainer.bootstrap = function (t, e) {
      return d("<ul/>").addClass("pagination").append(e);
    }),
    n
  );
});
