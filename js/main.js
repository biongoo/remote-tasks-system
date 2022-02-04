var ajaxLoadTimeout;
var uploading;
var errorAlert;

$.ajaxQ = (function(){
    var id = 0, Q = {};
  
    $(document).ajaxSend(function(e, jqx){
      jqx._id = ++id;
      Q[jqx._id] = jqx;
    });
    $(document).ajaxComplete(function(e, jqx){
      delete Q[jqx._id];
    });
  
    return {
      abortAll: function(){
        var r = [];
        $.each(Q, function(i, jqx){
          r.push(jqx._id);
          jqx.abort();
        });
        return r;
      }
    };
  
  })();

$(document).ajaxStart(function() {
    if(uploading) {
        ajaxLoadTimeout = setTimeout(function() { 
            if(errorAlert != 1) {
                $.ajaxQ.abortAll();
                errorAlert = 1;
                uploading.modal('hide');
                bootbox.dialog({ 
                    title: "Błąd",
                    message: 'Wystąpił błąd podczas wysyłania plików! Aby kontynuować odśwież stronę oraz sprawdź swoje połączenie internetowe.', 
                    closeButton: false,
                    size: 'large',
                    buttons: {
                        fee: {
                            label: 'Odśwież',
                            className: 'btn-outline-success',
                            callback: function(){
                                window.location.reload(true);
                            }
                        }
                    }
                })
            }
            clearTimeout(ajaxLoadTimeout);
        }, 60000);
    }
    else {
        ajaxLoadTimeout = setTimeout(function() { 
            if(errorAlert != 1) {
                $.ajaxQ.abortAll();
                errorAlert = 1;
                bootbox.dialog({ 
                    title: "Błąd",
                    message: 'Wystąpił błąd podczas połączenia z serwerem! Aby kontynuować odśwież stronę.', 
                    closeButton: false,
                    size: 'large',
                    buttons: {
                        fee: {
                            label: 'Odśwież',
                            className: 'btn-outline-success',
                            callback: function(){
                                window.location.reload(true);
                            }
                        }
                    }
                })
                clearTimeout(ajaxLoadTimeout);
            }
        }, 10000);
    }

}).ajaxSuccess(function() {
    clearTimeout(ajaxLoadTimeout);
});

function addHomework() {
    $.post('inc/actions.php', { showSubjects : 1 }, function(response){ 
        if(response == 0)
            bootbox.alert({ message: 'Coś poszło nie tak! Odśwież stronę!' });
        else {
            if(response == '<option value=""></option>') {
                bootbox.alert({ message: 'Najpierw dodaj przedmioty w zakładce "Przedmioty"!' });
            }
            else {
                let text = '<div class="form-group">';
                    text += '<label for="addHMSubject">Przedmiot:</label>';
                    text += '<select class="form-control" id="addHMSubject" required>';
                    text += response;
                    text += '</select>';
                text += '</div>';
                
                text += '<div class="form-group">';
                    text += '<label for="addHMDate">Data dodania zadania:</label>';
                    text += '<input type="date" class="form-control" id="addHMDate">';
                    text += '<small class="form-text">Jeżeli nie edytujesz daty, zostanie ona ustawiona na dzisiejszą.</small>';
                text += '</div>';

                text += '<div class="form-group">';
                    text += '<label for="addHMEndline">Deadline zadania:</label>';
                    text += '<input type="date" class="form-control" id="addHMEndline">';
                    text += '<small class="form-text">Nieobowiązkowa data, do której możesz oddać zadanie.</small>';
                text += '</div>';

                text += '<div class="form-group">';
                    text += '<label for="addHMDescription">Treść zadania:</label>';
                    text += '<textarea class="form-control" id="addHMDescription" rows="3" required></textarea>';
                text += '</div>';

                text += '<div class="form-group">';
                    text += '<label for="multiFiles">Dodatkowe pliki: </label>';
                    text += '<input type="file" id="multiFiles" multiple="multiple"/>';
                text += '</div>';

                bootbox.dialog({
                    message: text,
                    size: 'large',
                    buttons: {
                        cancel: {
                            label: "Wyjdź",
                            className: 'btn-outline-info'
                        },
                        insert: {
                            label: "Dodaj",
                            className: 'btn-outline-success',
                            callback: function(){
                                var x = 0;
                                var addHomework = $("#addHMSubject");
                                var addHomeworkName = $("#addHMSubject option:selected").text();
                                var addHMDate = $("#addHMDate").val();
                                var addHMEndline = $("#addHMEndline").val();
                                var addHMDescription = $("#addHMDescription").val();
                                var addHMDescription2 = $("#addHMDescription");

                                if(!addHomework[0].checkValidity()) {
                                    if(!$("#nameHelp").length)
                                        addHomework.parent().append('<small id="nameHelp" class="form-text text-danger">To pole nie może być puste!</small>')
                                    x = 1;
                                }
                                else
                                    if($("#nameHelp").length)
                                        $("#nameHelp").remove();

                                if(!addHMDescription2[0].checkValidity()) {
                                    if(!$("#nameHelp2").length)
                                        addHMDescription2.parent().append('<small id="nameHelp2" class="form-text text-danger">To pole nie może być puste!</small>')
                                    x = 1;
                                }
                                else
                                    if($("#nameHelp2").length)
                                        $("#nameHelp2").remove();

                                if(x == 1)
                                    return false;

                                var form_data = new FormData();

                                form_data.append('addHomework', addHomework.val());
                                form_data.append('addHMDate', addHMDate);
                                form_data.append('addHMEndline', addHMEndline);
                                form_data.append('addHMDescription', addHMDescription);

                                var ins = document.getElementById('multiFiles').files.length;
                                for (var x = 0; x < ins; x++) {
                                    form_data.append("files[]", document.getElementById('multiFiles').files[x]);
                                }

                                bootbox.hideAll();

                                if(ins) {
                                    uploading = bootbox.dialog({ 
                                        message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Wgrywanie plików...</div>', 
                                        closeButton: false 
                                    });
                                    var time = 500;
                                }
                                else
                                    var time = 1;

                                setTimeout(function() {
                                    $.ajax({
                                        url: 'inc/actions.php',
                                        dataType: 'text',
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        data: form_data,
                                        type: 'post',
                                        success: function (response) {
                                            let obj = JSON.parse(response);
                                            if(obj[0] == 0) {
                                                if(uploading) uploading.modal('hide');
                                                bootbox.alert({ message: obj[1] });
                                                uploading = 0;
                                            }
                                            else  {
                                                if($("#tableOfHomeworks").length) {
                                                    let textx = '<tr class="homTR" style ="display: none;" id="homTR' + obj[1] + '">';
                                                        textx += '<th style="display: none;">' + obj[1] + '</th>';

                                                        if(addHMEndline) {
                                                            let sub = (stringToDate(addHMEndline) - stringToDate(new Date().toISOString().slice(0, 10))) / 86400000;

                                                            if(sub <= 1)
                                                                textx += '<td class="text-nowrap text-danger">' + addHMEndline + '</td>';
                                                            else if(sub <= 3)
                                                                textx += '<td class="text-nowrap text-warning">' + addHMEndline + '</td>';
                                                            else
                                                                textx += '<td class="text-nowrap">' + addHMEndline + '</td>';
                                                        }
                                                        else
                                                            textx += '<td class="text-nowrap">Brak</td>';

                                                        textx += '<td>' + addHomeworkName + '</td>';
                                                        textx += '<td>' + addHMDescription + '</td>';
                                                    textx += '</tr>';

                                                    if($("#tableOfHomeworks").children().eq(obj[2]).length)
                                                        $("#tableOfHomeworks").children().eq(obj[2]).before(textx);
                                                    else
                                                        $('#tableOfHomeworks').last().append(textx);

                                                    $('#scrTR').parent().append('<script>$("#homTR'+obj[1]+'").on("click", function(){ showHomework($(this)); })</script>');
                                                    if(uploading) uploading.modal('hide');
                                                    $('#homTR' + obj[1]).fadeIn("slow");
                                                    uploading = 0;
                                                }
                                                else {
                                                    if(uploading) uploading.modal('hide');
                                                    change('main');
                                                    uploading = 0;
                                                }
                                            }
                                        }
                                    });
                                }, time);
                            }
                        }
                    }
                });
            }
        }
    });
    
    
}

function addSubject() {
    var lastTR = $('#tableOfSubjects').last();

    let text = '<div class="form-group">';
        text += '<label for="addName">Nazwa przedmiotu:</label>';
        text += '<input type="name" class="form-control" id="addName" required>';
    text += '</div>';
    
    text += '<div class="form-group">';
        text += '<label for="addTeacher">Nauczyciel:</label>';
        text += '<input type="name" class="form-control" id="addTeacher" required>';
    text += '</div>';

    text += '<div class="form-group">';
        text += '<label for="addGroup">Grupa:</label>';
        text += '<input type="name" class="form-control" id="addGroup">';
        text += '<small id="teacherHelp" class="form-text text-danger">Niewymagane</small>';
    text += '</div>';

    text += '<div class="form-group">';
        text += '<label for="addDescription">Opis:</label>';
        text += '<input type="name" class="form-control" id="addDescription">';
        text += '<small id="teacherHelp" class="form-text text-danger">Niewymagane</small>';
    text += '</div>';

    bootbox.dialog({
        message: text,
        size: 'large',
        buttons: {
            cancel: {
                label: "Wyjdź",
                className: 'btn-outline-info'
            },
            insert: {
                label: "Dodaj",
                className: 'btn-outline-success',
                callback: function(){
                    let x = 0;
                    var addName = $("#addName");
                    var addTeacher = $("#addTeacher");
                    var addGroup = $("#addGroup").val();
                    var addDescription = $("#addDescription").val();

                    if(!addName[0].checkValidity()) {
                        if(!$("#nameHelp").length)
                            $("#addName").parent().append('<small id="nameHelp" class="form-text text-danger">To pole nie może być puste!</small>')
                        x = 1;
                    }
                    else
                        if($("#nameHelp").length)
                            $("#nameHelp").remove();

                    if(!addTeacher[0].checkValidity()) {
                        if(!$("#teacherHelp").length)
                            $("#addTeacher").parent().append('<small id="teacherHelp" class="form-text text-danger">To pole nie może być puste!</small>')
                        x = 1;
                    }
                    else
                        if($("#teacherHelp").length)
                            $("#teacherHelp").remove();

                    if(x == 1)
                        return false;

                    $.post('inc/actions.php', { addSubject: addName.val(), addSubjectTeacher: addTeacher.val(), addSubjectGroup: addGroup, addSubjectDescription: addDescription}, function(response){ 
                        if(response == 0)
                            bootbox.alert({ message: 'Coś poszło nie tak! Odśwież stronę!' });
                        else  {
                            if($("#tableOfSubjects").length) {
                                let textx = '<tr class="subTR" style="display: none;" id="subTR' + response + '">';
                                    textx += '<th style="display: none;">' + response + '</th>';
                                    textx += '<td>' + addName.val() + '</td>';
                                    textx += '<td>' + addTeacher.val() + '</td>';
                                    textx += '<td>' + addGroup + '</td>';
                                    textx += '<td>' + addDescription + '</td>';
                                textx += '</tr>';
                                lastTR.append(textx);
                                $('#scrTR').parent().append('<script>$("#subTR'+response+'").on("click", function(){ editSubjects($(this)); })</script>');
                                bootbox.hideAll();
                                $('#subTR' +response).fadeIn("slow");
                            }
                            else
                                change('subjects');
                        }
                    });
                }
            }
        }
    });
}

function addTheory() {
    $.post('inc/actions.php', { showSubjects : 1 }, function(response){ 
        if(response == 0)
            bootbox.alert({ message: 'Coś poszło nie tak! Odśwież stronę!' });
        else {
            if(response == '<option value=""></option>') {
                bootbox.alert({ message: 'Najpierw dodaj przedmioty w zakładce "Przedmioty"!' });
            }
            else {
                let text = '<div class="form-group">';
                    text += '<label for="addTHSubject">Przedmiot:</label>';
                    text += '<select class="form-control" id="addTHSubject" required>';
                    text += response;
                    text += '</select>';
                text += '</div>';
                
                text += '<div class="form-group">';
                    text += '<label for="addTHDate">Data dodania teorii:</label>';
                    text += '<input type="date" class="form-control" id="addTHDate">';
                    text += '<small class="form-text">Jeżeli nie edytujesz daty, zostanie ona ustawiona na dzisiejszą.</small>';
                text += '</div>';

                text += '<div class="form-group">';
                    text += '<label for="addTHDescription">Krótki opis:</label>';
                    text += '<textarea class="form-control" id="addTHDescription" rows="3" required></textarea>';
                text += '</div>';

                text += '<div class="form-group">';
                    text += '<label for="multiHMFiles">Pliki z teorią: </label>';
                    text += '<input type="file" id="multiHMFiles" multiple="multiple"/>';
                text += '</div>';

                bootbox.dialog({
                    message: text,
                    size: 'large',
                    buttons: {
                        cancel: {
                            label: "Wyjdź",
                            className: 'btn-outline-info'
                        },
                        insert: {
                            label: "Dodaj",
                            className: 'btn-outline-success',
                            callback: function(){
                                var x = 0;
                                var addTheory = $("#addTHSubject");
                                var addTheoryName = $("#addTHSubject option:selected").text();
                                var addTHDate = $("#addTHDate").val();
                                var addTHDescription = $("#addTHDescription").val();
                                var addTHDescription2 = $("#addTHDescription");

                                if(!addTheory[0].checkValidity()) {
                                    if(!$("#nameHelp").length)
                                        addTheory.parent().append('<small id="nameHelp" class="form-text text-danger">To pole nie może być puste!</small>')
                                    x = 1;
                                }
                                else
                                    if($("#nameHelp").length)
                                        $("#nameHelp").remove();

                                if(!addTHDescription2[0].checkValidity()) {
                                    if(!$("#nameHelp2").length)
                                        addTHDescription2.parent().append('<small id="nameHelp2" class="form-text text-danger">To pole nie może być puste!</small>')
                                    x = 1;
                                }
                                else
                                    if($("#nameHelp2").length)
                                        $("#nameHelp2").remove();

                                if(x == 1)
                                    return false;

                                var form_data = new FormData();

                                form_data.append('addTheory', addTheory.val());
                                form_data.append('addTHDate', addTHDate);
                                form_data.append('addTHDescription', addTHDescription);

                                var ins = document.getElementById('multiHMFiles').files.length;
                                for (var x = 0; x < ins; x++) {
                                    form_data.append("files[]", document.getElementById('multiHMFiles').files[x]);
                                }

                                bootbox.hideAll();

                                if(ins) {
                                    var dialog = bootbox.dialog({ 
                                        message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Wgrywanie plików...</div>', 
                                        closeButton: false 
                                    });
                                    var time = 500;
                                }
                                else
                                    var time = 1;

                                setTimeout(function() {
                                    $.ajax({
                                        url: 'inc/actions.php',
                                        dataType: 'text',
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        data: form_data,
                                        type: 'post',
                                        success: function (response) {
                                            let obj = JSON.parse(response);
                                            if(obj[0] == 0) {
                                                if(dialog) dialog.modal('hide');
                                                bootbox.alert({ message: obj[1] });
                                            }
                                            else  {
                                                if($("#tableOfTheory").length) {
                                                    let textx = '<tr class="theTR" style ="display: none;" id="theTR' + obj[1] + '">';
                                                        textx += '<th style="display: none;">' + obj[1] + '</th>';
                                                        if(addTHDate)
                                                            textx += '<td class="text-nowrap">' + addTHDate + '</td>';
                                                        else
                                                            textx += '<td class="text-nowrap">' + new Date().toISOString().slice(0, 10) + '</td>';
                                                        textx += '<td>' + addTheoryName + '</td>';
                                                        textx += '<td>' + addTHDescription + '</td>';
                                                    textx += '</tr>';

                                                    if($("#tableOfTheory").children().eq(obj[2]).length)
                                                        $("#tableOfTheory").children().eq(obj[2]).before(textx);
                                                    else
                                                        $('#tableOfTheory').last().append(textx);

                                                    $('#scrTR').parent().append('<script>$("#theTR'+obj[1]+'").on("click", function(){ showTheory($(this)); })</script>');
                                                    if(dialog) dialog.modal('hide');
                                                    $('#theTR' + obj[1]).fadeIn("slow");
                                                }
                                                else {
                                                    if(dialog) dialog.modal('hide');
                                                    change('theory');
                                                }
                                            }
                                        }
                                    });
                                }, time);
                            }
                        }
                    }
                });
            }
        }
    });
}

function change(name) {
    $.ajax({
        type: "POST",
        url: "inc/actions.php",
        data: {changePage: name},
        success: function(response){
            $.when($('#main').fadeOut())
                .then(function(){
                    $('#main').html(response);
                    $("#main").fadeIn();
                    history.pushState(null, '', '/?'+name); 
                    $('#navbarSupportedContent').collapse('hide'); 
                });
        }
     });
}

function downloadFile(nameOfFile, homId) {
    $.post('inc/actions.php', {downloadFile: nameOfFile, downloadHomId: homId}, function(response){  
        if(response != 1) {
            bootbox.hideAll();
            bootbox.alert(response);
        }
        else
            window.location.href = "inc/download.php?downloadFile=" + nameOfFile.split("+").join("%2B") + "&downloadHomId=" + homId;
    });
}

function downloadFileTheory(nameOfFile, theId) {
    $.post('inc/actions.php', {downloadFileTheory: nameOfFile, downloadTheId: theId}, function(response){  
        if(response != 1) {
            bootbox.hideAll();
            bootbox.alert(response);
        }
        else
            window.location.href = "inc/download.php?downloadFileTheory=" + nameOfFile.split("+").join("%2B") + "&downloadTheId=" + theId;
    });
}

function editSubjects(subTR) {
    var id = subTR.children().eq(0).text();
    var name = subTR.children().eq(1).text();
    var teacher = subTR.children().eq(2).text();
    var group = subTR.children().eq(3).text();
    var description = subTR.children().eq(4).text();

    bootbox.dialog({
        message: '<b>'+name+' ('+teacher+') --> Co zamierasz z tym zrobić?</b>',
        size: 'large',
        buttons: {
            delete: {
                label: "Usuń",
                className: 'btn-outline-danger',
                callback: function(){
                    bootbox.confirm("Czy aby na pewno chcesz usunąć? Zostaną również usunięte wszystkie wpisy związane z tym przedmiotem.", function(result){ 
                        if(result) {
                            $.post('inc/actions.php', {deleteSubject: id}, function(response){ 
                                if(response == 0)
                                    bootbox.alert({ message: 'Coś poszło nie tak! Odśwież stronę!' });
                                else if(response == 1) {
                                    if(!$("#tableOfSubjects").children().eq(1).length)
                                            change('subjects');
                                    else {
                                        $.when($('#subTR'+id).fadeOut("slow"))
                                            .then(function(){
                                                $('#subTR'+id).remove();
                                            }); 
                                        bootbox.alert({ message: 'Usunięto pomyślnie!' });
                                    }
                                }
                            });
                        }
                    });
                }
            },
            edit: {
                label: "Edytuj",
                className: 'btn-outline-warning',
                callback: function() {
                    let text = '<div class="form-group">';
                        text += '<label for="newName">Nazwa przedmiotu:</label>';
                        text += '<input type="name" class="form-control" id="newName" value="' + name + '" required>';
                    text += '</div>';
                    
                    text += '<div class="form-group">';
                        text += '<label for="newTeacher">Nauczyciel:</label>';
                        text += '<input type="name" class="form-control" id="newTeacher" value="' + teacher + '" required>';
                    text += '</div>';

                    text += '<div class="form-group">';
                        text += '<label for="newGroup">Grupa:</label>';
                        text += '<input type="name" class="form-control" id="newGroup" value="' + group + '">';
                    text += '</div>';

                    text += '<div class="form-group">';
                        text += '<label for="newDescription">Opis:</label>';
                        text += '<input type="name" class="form-control" id="newDescription" value="' + description + '">';
                    text += '</div>';

                    bootbox.dialog({
                        message: text,
                        size: 'large',
                        buttons: {
                            cancel: {
                                label: "Wyjdź",
                                className: 'btn-outline-info'
                            },
                            edit: {
                                label: "Edytuj",
                                className: 'btn-outline-danger',
                                callback: function(){
                                    let y = 0;
                                    var newName = $("#newName");
                                    var newTeacher = $("#newTeacher");
                                    var newGroup = $("#newGroup").val();
                                    var newDescription = $("#newDescription").val();

                                    if(!newName[0].checkValidity()) {
                                        if(!$("#nameHelp").length)
                                            $("#newName").parent().append('<small id="nameHelp" class="form-text text-danger">To pole nie może być puste!</small>')
                                        y = 1;
                                    }
                                    else
                                        if($("#nameHelp").length)
                                            $("#nameHelp").remove();
                
                                    if(!newTeacher[0].checkValidity()) {
                                        if(!$("#teacherHelp").length)
                                            $("#newTeacher").parent().append('<small id="teacherHelp" class="form-text text-danger">To pole nie może być puste!</small>')
                                        y = 1;
                                    }
                                    else
                                        if($("#teacherHelp").length)
                                            $("#teacherHelp").remove();
                
                                    if(y == 1)
                                        return false;

                                    $.post('inc/actions.php', {editSubject: id, editSubjectName: newName.val(), editSubjectTeacher: newTeacher.val(), editSubjectGroup: newGroup, editSubjectDescription: newDescription}, function(response){ 
                                        if(response == 0)
                                            bootbox.alert({ message: 'Nic nie zostało zmienione, bądź coś poszło nie tak!' });
                                        else if(response == 1) {
                                            subTR.children().eq(1).text(newName.val()); subTR.children().eq(2).text(newTeacher.val()); subTR.children().eq(3).text(newGroup); subTR.children().eq(4).text(newDescription);
                                        }
                                    });
                                }
                            }
                        }
                    });
                }
            },
            ok: {
                label: "Wyjdź",
                className: 'btn-outline-info'
            }
        }
    });
}

function insertPasswd() {
    bootbox.prompt({ 
        title: "Wprowadź hasło:",
        closeButton: false,
        centerVertical: true,
        buttons: { confirm: { label: "Zaloguj", } },
        callback: function(result){ 
            login(result);
        }
    });
}

function login(result) {
    $.post('inc/actions.php', {passwd: result}, function(response){ 
        if(response == 0)
            bootbox.alert({ message: 'Nieprawidłowe hasło!', backdrop: true, centerVertical: true, callback: function () { insertPasswd(); } });
        else 
            bootbox.alert({ message: 'Zalogowano pomyślnie!', backdrop: true, centerVertical: true, callback: function () { 
                $.when($('#main').fadeOut())
                .then(function(){
                    $('#main').html(response);
                    $("#main").fadeIn();
                    history.pushState(null, '', '/?'+name); 
                    $('#navbarSupportedContent').collapse('hide'); 
                });  
            } 
        });
    });
}

function showHomework(homTR) {
    var id = homTR.children().eq(0).text();

    $.post('inc/actions.php', { showHomework: id }, function(response){ 
        var object = JSON.parse(response);
        if(object[3])
            var fileNames = JSON.parse(object[3]);

        text = '<div class="card">';
            text += '<ul class="list-group list-group-flush">';
                text += '<li class="list-group-item">';
                    text += '<h5 class="card-title">Przedmiot:</h5>';
                    text += '<p class="card-text">' + object[4] + '</p>';
                text += '</li>';

                text += '<li class="list-group-item">';
                    text += '<h5 class="card-title">Data dodania:</h5>';
                    text += '<p class="card-text">' + object[0] + '</p>';
                text += '</li>';

                text += '<li class="list-group-item">';
                    text += '<h5 class="card-title">Deadline:</h5>';
                    text += '<p class="card-text">';
                        text +=  (!object['endline']) ? 'Brak' : object[1];
                    text += '</p>';
                text += '</li>';

                text += '<li class="list-group-item">';
                    text += '<h5 class="card-title">Treść:</h5>';
                    text += '<p class="card-text">' + object[2] + '</p>';
                text += '</li>';

                text += '<li class="list-group-item">';
                    text += '<h5 class="card-title">Pliki:</h5>';

                    if(fileNames)
                        for(i = 0; i < fileNames.length; i++)
                            text += '<a href="javascript:void(0)" onclick="downloadFile(\'' + fileNames[i] + '\', ' + id + ');" class="list-group-item list-group-item-action">' + fileNames[i] + '</a>';
                    else
                        text += 'Brak';
                        
                text += '</li>';
            text += '</ul>';
        text += '</div>';

        bootbox.dialog({
            message: text,
            size: 'large',
            buttons: {
                delete: {
                    label: "Usuń",
                    className: 'btn-outline-danger',
                    callback: function(){
                        bootbox.confirm("Czy aby na pewno chcesz usunąć? Zostaną również usunięte wszystkie pliki, jeżeli istnieją.", function(result){ 
                            if(result) {
                                $.post('inc/actions.php', {deleteHome: id}, function(response){ 
                                    if(response == 0)
                                        bootbox.alert({ message: 'Coś poszło nie tak!' });
                                    else if(response == 1) {
                                        if(!$("#tableOfHomeworks").children().eq(1).length)
                                                change('main');
                                        else {
                                            $.when($('#homTR'+id).fadeOut("slow"))
                                                .then(function(){
                                                    $('#homTR'+id).remove();
                                                }); 
                                            bootbox.alert({ message: 'Usunięto pomyślnie!' });
                                        }
                                    }
                                });
                            }
                        });
                    }
                },
                archive: {
                    label: "Zarchiwizuj",
                    className: 'btn-outline-warning',
                    callback: function(){
                        bootbox.alert('Funckja niedostępna');
                    }
                },
                edit: {
                    label: "Edytuj",
                    className: 'btn-outline-secondary',
                    callback: function(){
                        bootbox.alert('Funckja niedostępna');
                    }
                },
                cancel: {
                    label: "Wyjdź",
                    className: 'btn-outline-primary'
                }
            }
        }); 
    });
}

function showTheory(theTR) {
    var id = theTR.children().eq(0).text();

    $.post('inc/actions.php', { showTheory: id }, function(response){ 
        var object = JSON.parse(response);
        if(object[2])
            var fileNames = JSON.parse(object[2]);

        text = '<div class="card">';
            text += '<ul class="list-group list-group-flush">';
                text += '<li class="list-group-item">';
                    text += '<h5 class="card-title">Przedmiot:</h5>';
                    text += '<p class="card-text">' + object[3] + '</p>';
                text += '</li>';

                text += '<li class="list-group-item">';
                    text += '<h5 class="card-title">Data dodania:</h5>';
                    text += '<p class="card-text">' + object[0] + '</p>';
                text += '</li>';

                text += '<li class="list-group-item">';
                    text += '<h5 class="card-title">Treść:</h5>';
                    text += '<p class="card-text">' + object[1] + '</p>';
                text += '</li>';

                text += '<li class="list-group-item">';
                    text += '<h5 class="card-title">Pliki:</h5>';

                    if(fileNames)
                        for(i = 0; i < fileNames.length; i++)
                            text += '<a href="javascript:void(0)" onclick="downloadFileTheory(\'' + fileNames[i] + '\', ' + id + ');" class="list-group-item list-group-item-action">' + fileNames[i] + '</a>';
                    else
                        text += 'Brak';
                        
                text += '</li>';
            text += '</ul>';
        text += '</div>';

        bootbox.dialog({
            message: text,
            size: 'large',
            buttons: {
                delete: {
                    label: "Usuń",
                    className: 'btn-outline-danger',
                    callback: function(){
                        bootbox.confirm("Czy aby na pewno chcesz usunąć? Zostaną również usunięte wszystkie pliki, jeżeli istnieją.", function(result){ 
                            if(result) {
                                $.post('inc/actions.php', {deleteTheory: id}, function(response){ 
                                    if(response == 0)
                                        bootbox.alert({ message: 'Coś poszło nie tak!' });
                                    else if(response == 1) {
                                        if(!$("#tableOfTheory").children().eq(1).length)
                                                change('theory');
                                        else {
                                            $.when($('#theTR'+id).fadeOut("slow"))
                                                .then(function(){
                                                    $('#theTR'+id).remove();
                                                }); 
                                            bootbox.alert({ message: 'Usunięto pomyślnie!' });
                                        }
                                    }
                                });
                            }
                        });
                    }
                },
                archive: {
                    label: "Zarchiwizuj",
                    className: 'btn-outline-warning',
                    callback: function(){
                        bootbox.alert('Funckja niedostępna');
                    }
                },
                edit: {
                    label: "Edytuj",
                    className: 'btn-outline-secondary',
                    callback: function(){
                        bootbox.alert('Funckja niedostępna');
                    }
                },
                cancel: {
                    label: "Wyjdź",
                    className: 'btn-outline-primary'
                }
            }
        }); 
    });
}

function stringToDate(s) {
    var dateParts = s.split(' ')[0].split('-'); 
    var d = new Date(dateParts[0], --dateParts[1], dateParts[2]);
  
    return d
}
