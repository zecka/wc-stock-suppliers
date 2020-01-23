import $ from "./jquery";
export default class SuppliersOrderActions {
  constructor() {
    this.$metabox = $("#wcss-supplier-order-actions");
    if (!this.$metabox[0]) return;
    this.$btn = this.$metabox.find("#wcss-action-submit");
    this.$btnPreview = this.$metabox.find("#wcss-preview-email");
    this.$input = this.$metabox.find("#wcss-action-value");
    this.$form = $("form#post");
    this.$previewBlock = $("#acf-wcss_group_supplier_email_preview");
    this.handleActionClick();
    this.$previewBlock.hide();
  }
  handleActionClick() {
    this.$btn.on("click", () => {
      const ok = confirm("Are you sur you want to: " + this.$btn.html());
      if (ok) {
        this.$input.val(this.$btn.data("action"));
        this.$form.submit();
      }
      return false;
    });

    this.$btnPreview.on("click", () => {
      this.previewEmail();
      return false;
    });
  }
  previewEmail() {
    const data = {
      action: "wcss_preview_email",
      nonce: this.$btnPreview.data("nonce"),
      post_id: this.$btnPreview.data("id")
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
        this.$previewBlock.slideDown();
        this.$previewBlock.removeClass("closed");
        $("html,body").animate(
          { scrollTop: this.$previewBlock.offset().top - 200 },
          "slow"
        );
        this.$previewBlock.find(".inside").html(response.data.preview);
      }
    });
  }
}
