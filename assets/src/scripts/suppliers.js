import $ from "./jquery";
export default class Suppliers {
  constructor() {
    this.handleClickBtnGet();
    this.suppliers = [];
    this.isFetching = false;
    this.currentPage = 1;
    this.maxPages = 1;
    this.nonce = $(".wcss-get-suppliers").data("nonce");
    this.handleClickGenerate();
  }
  handleClickBtnGet() {
    $(".wcss-get-suppliers").on("click", () => {
      if (!this.isFetching) {
        this.isFetching = true;
        this.suppliers = [];
        this.currentPage = 1;
        this.getSuppliers();
      }
    });
  }
  handleClickGenerate() {
    $("#suppliers").on("click", ".wcss-generate-order", e => {
      const $this = $(e.currentTarget);
      const suppliersIds = $this.data("suppliers");
      const data = {
        action: "wcss_generate_supplier_order",
        nonce: $this.data("nonce"),
        suppliers: suppliersIds
      };
      $.ajax({
        type: "post",
        url: ajaxurl,
        data: data,
        error: (response, error) => {
          console.error("response", response);
          console.error("error", error);
        },
        success: response => {
          const $item = $this.closest(".supplier-item");
          $item.html("Success");
          setTimeout(() => {
            $item.slideUp();
          }, 1500);
        }
      });
    });
  }
  getSuppliers() {
    $("#suppliers").html("");
    const data = {
      action: "wcss_get_suppliers_stocks",
      nonce: this.nonce,
      paged: this.currentPage
    };
    $.ajax({
      type: "post",
      url: ajaxurl,
      data: data,
      error: (response, error) => {
        console.error("response", response);
        console.error("error", error);
      },
      success: response => {
        const { suppliers, max_num_pages, paged } = response.data;
        this.suppliers = suppliers.concat(this.suppliers);
        if (max_num_pages > paged) {
          this.currentPage++;
          this.getSuppliers();
        } else {
          if (this.suppliers.length < 1) {
            alert("nothing found");
          }
          this.appendSupplier();
        }
      }
    });
  }
  appendSupplier() {
    this.suppliers.forEach(supplier => {
      $("#suppliers").append(supplier.html);
      this.isFetching = false;
    });
  }
}
