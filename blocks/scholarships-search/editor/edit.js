import { default as DataSource } from "../../../assets/src/block-controls/data-source";

const { InspectorControls } = wp.blockEditor;
const { PanelBody } = wp.components;

const Edit = (props) => {
  const { className, attributes, setAttributes } = props;

  return (
    <>
      <InspectorControls>
        <PanelBody title="Scholarships Search Settings" initialOpen={true}>
          <DataSource
            label="Data Source"
            help=""
            attributes={attributes}
            setAttributes={setAttributes}
          />
        </PanelBody>
      </InspectorControls>

      <div className={`wsu-gutenberg-scholarships-search ${className}`}>
        <div className="wsu-gutenberg-scholarships-search__filters">
          <div className="wsu-gutenberg-scholarships-search__filter">
            Current grade level
            <span className="wsu-gutenberg-scholarships-search__filter-icon dashicons dashicons-arrow-down-alt2"></span>
          </div>
          <div className="wsu-gutenberg-scholarships-search__filter">
            G.P.A.
          </div>
          <div className="wsu-gutenberg-scholarships-search__filter">
            Citizenship
            <span className="wsu-gutenberg-scholarships-search__filter-icon dashicons dashicons-arrow-down-alt2"></span>
          </div>
          <div className="wsu-gutenberg-scholarships-search__filter">
            Residency
            <span className="wsu-gutenberg-scholarships-search__filter-icon dashicons dashicons-arrow-down-alt2"></span>
          </div>
          <div className="wsu-gutenberg-scholarships-search__button">Go</div>
        </div>
      </div>
    </>
  );
};

export default Edit;
