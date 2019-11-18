let {Component} = React;
let {__} = wp.i18n;
let {Fragment} = wp.element;
let {PluginPrePublishPanel} = wp.editPost;
let {registerPlugin} = wp.plugins;

class PPChecklistsWarning extends Component {
    constructor () {
        super();

        this.updateFailedRequirements = this.updateFailedRequirements.bind(this);

        this.state = {
            requirements: []
        };

        wp.hooks.addAction('pp-checklists.update-failed-requirements', 'publishpress/checklists', this.updateFailedRequirements, 10);
    };

    updateFailedRequirements (failedRequirements) {
        this.setState({requirements: failedRequirements});
    };

    render () {
        if (this.state.requirements.length === 0) {
            return (null);
        }

        return (<Fragment>
            <PluginPrePublishPanel
                name="gutenberg-boilerplate-sidebar"
                title={__('Checklist')}
                initialOpen="true"
            >
                <div class="pp-checklists-failed-requirements-warning">
                    <p>{__('Are you sure you want to publish anyway?', 'publishpress-checklists')}</p>
                    <ul>
                        {this.state.requirements.map((item, i) => <li><span
                            className="dashicons dashicons-no"></span><span>{item}</span></li>)}
                    </ul>
                </div>
            </PluginPrePublishPanel>
        </Fragment>);
    }
}

registerPlugin('pp-checklists-warning', {
    icon: 'warning',
    render: () => (<PPChecklistsWarning/>)
});

