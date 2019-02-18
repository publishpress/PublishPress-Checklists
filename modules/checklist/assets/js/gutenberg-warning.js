let {
  Component
} = React;
let {
  __
} = wp.i18n;
let {
  Fragment
} = wp.element;
let {
  PluginPrePublishPanel
} = wp.editPost;
let {
  registerPlugin
} = wp.plugins;

class PPChecklistWarning extends Component {
  constructor() {
    super();
    this.updateFailedRequirements = this.updateFailedRequirements.bind(this);
    this.state = {
      requirements: []
    };
    wp.hooks.addAction('publishpress-content-checklist.update-failed-requirements', 'publishpress/content-checklist', this.updateFailedRequirements, 10);
  }

  updateFailedRequirements(failedRequirements) {
    this.setState({
      requirements: failedRequirements
    });
  }

  render() {
    if (this.state.requirements.length === 0) {
      return null;
    }

    return wp.element.createElement(Fragment, null, wp.element.createElement(PluginPrePublishPanel, {
      name: "gutenberg-boilerplate-sidebar",
      title: __('Checklist'),
      initialOpen: "true"
    }, wp.element.createElement("div", {
      class: "pp-checklist-failed-requirements-warning"
    }, wp.element.createElement("p", null, __('The following requirements are not completed yet. Are you sure you want to publish', 'publishpress-content-checklist')), wp.element.createElement("ul", null, this.state.requirements.map((item, i) => wp.element.createElement("li", null, wp.element.createElement("span", {
      className: "dashicons dashicons-no"
    }), wp.element.createElement("span", null, item)))))));
  }

}

registerPlugin('publishpress-content-checklist-warning', {
  icon: 'admin-site',
  render: () => wp.element.createElement(PPChecklistWarning, null)
});
//# sourceMappingURL=gutenberg-warning.js.map