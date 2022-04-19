/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';
$(document).ready()
{
    const removeFormButton = document.createElement('button');
    removeFormButton.innerText = 'Delete this tag';
    removeFormButton.className = "btn btn-danger";
    removeFormButton.id="deleteBtn";
    $('#task_collection_tasks').find('.btn-success').parent().append(removeFormButton);

    $('.btn-danger').on('click', function(){
        $(this).parent().parent().parent().remove();
        $("form[name='task_collection']").submit();
    });

    $('.btn-remove-user').on('click', function(e){
        e.preventDefault();
        $(this).parent().parent().remove();
        $("form[name='user_collection']").submit();
    });

}