const { TextControl, SelectControl, PanelRow } = wp.components;
const { useState, useEffect } = wp.element;

function DataSource(props) {
  const { label, help, attributes, setAttributes } = props;

  function isValidUrl(base, path = "") {
    try {
      const url = new URL(path, base);
      return (
        Boolean(url) &&
        (url.hostname.includes("wsu.edu") || url.hostname.includes(".local"))
      );
    } catch (e) {
      return false;
    }
  }

  function handleCustomDataSource(url) {
    if (url === "" || isValidUrl(url)) {
      setAttributes({ custom_data_source: url });
    }
  }

  return (
    <div className="wsu-gutenberg-scholarships__data-source">
      <SelectControl
        label={label}
        help={help}
        value={attributes.data_source}
        options={[
          {
            label: "This Site",
            value: "local",
            disabled: !WSUWP_SCHOLARSHIPS_PLUGIN_DATA.postTypeEnabled,
          },
          { label: "Custom", value: "custom" },
        ]}
        onChange={(newval) => setAttributes({ data_source: newval })}
      />

      {attributes.data_source === "custom" && (
        <TextControl
          label="Custom Data Source"
          help="Site URL for to pull people data from."
          placeholder="https://example.wsu.edu"
          defaultValue={attributes.custom_data_source}
          onChange={(url) => handleCustomDataSource(url)}
        />
      )}

      {attributes.data_source === "custom" &&
        !isValidUrl(attributes.custom_data_source) && (
          <div className="wsu-gutenberg-scholarships__data-source-notice notice notice-error notice-alt">
            <p>Error: A valid data source is required.</p>
          </div>
        )}
    </div>
  );
}

export default DataSource;
