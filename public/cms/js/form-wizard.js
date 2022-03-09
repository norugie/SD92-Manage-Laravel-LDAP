$(function() {

    var form = $('#wizard_form').show();

    form.steps({
        headerTag: "h3",
        bodyTag: "fieldset",
        titleTemplate: "#title#",
        transitionEffect: 'slideLeft',
        onInit: function(event) {
            $.AdminBSB.input.activate();

            //Set tab width
            var $tab = $(event.currentTarget).find('ul[role="tablist"] li');
            var tabCount = $tab.length;
            $tab.css('width', (100 / tabCount) + '%');

            //set button waves effect
            setButtonWavesEffect(event);
        },
        onStepChanging: function() {
            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        },
        onFinishing: function() {
            form.validate().settings.ignore = ":disabled";
            return form.valid();
        },
        onFinished: function() {
            $('#ctr_value_event').attr('value', ctr_event);
            form.submit();
        }
    });

});

function setButtonWavesEffect(event) {
    $(event.currentTarget).find('[role="menu"] li a').removeClass('waves-effect');
    $(event.currentTarget).find('[role="menu"] li:not(.disabled) a').addClass('waves-effect');
}