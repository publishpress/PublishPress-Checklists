const { Component } = React;

const { __ } = wp.i18n;

const { Fragment } = wp.element;

const { PluginPrePublishPanel } = wp.editPost;

const { registerPlugin } = wp.plugins;

class PPChecklistWarning extends Component {
    constructor () {
        super();

        this.updateFailedRequirements = this.updateFailedRequirements.bind(this);

        this.state = {
            requirements: []
        };

        wp.hooks.addAction('publishpress-content-checklist.update-failed-requirements', 'publishpress/content-checklist', this.updateFailedRequirements, 10);
    };

    updateFailedRequirements(failedRequirements) {
        this.setState({requirements: failedRequirements});
    };

    render() {
        if (this.state.requirements.length === 0) {
            return (null);
        }

        return ( <Fragment>
            <PluginPrePublishPanel
                name="gutenberg-boilerplate-sidebar"
                title={ __( 'Checklist' ) }
                initialOpen="true"
            >
                <div class="pp-checklist-failed-requirements-warning">
                    <p>{ __('The following requirements are not completed yet. Are you sure you want to publish', 'publishpress-content-checklist') }</p>
                    <ul>
                        {this.state.requirements.map((item, i) => <li><span className="dashicons dashicons-no"></span><span>{ item }</span></li>)}
                    </ul>
                </div>
            </PluginPrePublishPanel>
        </Fragment> );
    }
}

registerPlugin( 'publishpress-content-checklist-warning', {
    icon: 'admin-site',
    render: () => (<PPChecklistWarning />),
} );

