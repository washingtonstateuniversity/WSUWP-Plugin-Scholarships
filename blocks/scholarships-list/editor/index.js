import { registerBlockType } from "@wordpress/blocks";

import Edit from "./edit";

registerBlockType("wsuwp/scholarships-list", {
  title: "Scholarships",
  icon: "awards",
  category: "common",
  attributes: {
    data_source: {
      type: "string",
      default: "custom",
    },
    custom_data_source: {
      type: "string",
      default: "",
    },
  },
  edit: Edit,
  save: function () {
    return null;
  },
});
