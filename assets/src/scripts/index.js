import "../styles/index.scss";
import Suppliers from "./suppliers";
import SupplierOrderActions from "./supplierOrderActions";
var $ = jQuery;
$(document).ready(() => {
  new Suppliers();
  new SupplierOrderActions();
});
