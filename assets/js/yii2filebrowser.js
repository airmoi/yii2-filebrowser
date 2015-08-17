(function ($) {

    $.fn.filebrowser = function (options) {
        var settings = $.extend({
            // These are the defaults.
            token: "",
            breadcrumbs: ".breadcrumbs",
            items: ".items",
            route: "?r=filebrowser/browser/",
            permissions: {
                upload:false,
                delete:false,
                createdir:false,
                subdir:true
            },
        }, options);
        
        var filebrowser = $(this),
            breadcrumbs = filebrowser.find(settings.breadcrumbs),
            fileList = filebrowser.find(settings.items);
    
        //Remove uploadform if upload not allowed
        if(!settings.permissions.upload){
            filebrowser.find('.upload-box').remove();
        }
        //Remove new dir form if create Dir not allowed
        if(!settings.permissions.createdir){
            filebrowser.find('.newdir').remove();
        }

        $.get(settings.route+'list&token='+settings.token, function(data) {

		var response = [data],
			currentPath = '',
			breadcrumbsUrls = [];

		var folders = [],
			files = [];

		// This event listener monitors changes on the URL. We use it to
		// capture back/forward navigation in the browser.

		$(window).on('hashchange', function(){

			goto(window.location.hash);

			// We are triggering the event. This will execute 
			// this function on page load, so that we show the correct folder:

		}).trigger('hashchange');


		// Hiding and showing the search box

		filebrowser.find('.search').click(function(){

			var search = $(this);

			search.find('span').hide();
			search.find('input[type=search]').show().focus();

		});


		// Listening for keyboard input on the search field.
		// We are using the "input" event which detects cut and paste
		// in addition to keyboard input.

		filebrowser.find('.search input').on('input', function(e){

			folders = [];
			files = [];

			var value = this.value.trim();

			if(value.length) {

				filebrowser.addClass('searching');

				// Update the hash on every key stroke
				window.location.hash = 'search=' + value.trim();

			}

			else {

				filebrowser.removeClass('searching');
				window.location.hash = encodeURIComponent(currentPath);

			}

		}).on('keyup', function(e){

			// Clicking 'ESC' button triggers focusout and cancels the search

			var search = $(this);

			if(e.keyCode == 27) {

				search.trigger('focusout');

			}

		}).focusout(function(e){

			// Cancel the search

			var search = $(this);

			if(!search.val().trim().length) {

				window.location.hash = encodeURIComponent(currentPath);
				search.hide();
				search.parent().find('span').show();

			}

		});


		// Clicking on folders
		fileList.on('click', 'li.folders', function(e){
			e.preventDefault();

			var nextDir = $(this).find('a.folders').attr('href');

			if(filebrowser.hasClass('searching')) {

				// Building the breadcrumbs

				breadcrumbsUrls = generateBreadcrumbs(nextDir);

				filebrowser.removeClass('searching');
				filebrowser.find('input[type=search]').val('').hide();
				filebrowser.find('span').show();
			}
			else {
				breadcrumbsUrls.push(nextDir);
			}

			window.location.hash = encodeURIComponent(nextDir);
			currentPath = nextDir;
		});
                
                //Clicking on file
                fileList.on('click', 'li.files', function(e){
			e.preventDefault();

			var path = $(this).find('a.files').attr('href');

			window.location = settings.route + 'download&file=' + path + '+&token=' + settings.token;
		});
                
                //Clicking on delete
                fileList.on('click', 'button.delete', function(e){
			e.preventDefault();
                        e.stopPropagation();

                        if(!settings.permissions.delete){
                            alert("Vous ne pouvez pas supprimer de document");
                            return false;
                        }
			var parent = $(this).parent()
                        var path = parent.attr('href');
                        
                        var choice = false;
                        if(parent.hasClass('folders')){
                            choice = confirm("supprimer le dossier " + parent.find('.name').html() + " et tout son contenu ?");
                        }
                        else {
                            choice = confirm("supprimer le fichier " + parent.find('.name').html() + "?");
                        }
                        
                        if ( !choice ){
                            alert("canceled");
                            return false;
                        }
                        
                        $.get(settings.route + 'delete&file=' + path + '+&token=' + settings.token, function(data){
                            if(data.success){
                                window.location.reload();
                            } else {
                                alert(data.message);
                            }
                        })
			//window.location = settings.route + 'download&delete=' + path + '+&token=' + settings.token;
		});


		// Clicking on breadcrumbs
		breadcrumbs.on('click', 'a', function(e){
			e.preventDefault();

			var index = breadcrumbs.find('a').index($(this)),
				nextDir = breadcrumbsUrls[index];

			breadcrumbsUrls.length = Number(index);

			window.location.hash = encodeURIComponent(nextDir);

		});
                
                //upload
                filebrowser.find('.upload-box form').on('beforeSubmit', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var data = new FormData(document.getElementById($(this).attr('id')));
                    
                    console.log(data);
                    
                    $.ajax(settings.route + 'upload&token=' + settings.token + '&path=' + currentPath, {
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        data: data
                    }).done(function(data){
                         if(data.success){
                                window.location.reload();
                            } else {
                                alert(data.message);
                            }
                    });
                    return false;
                })
                
                //new dir
                filebrowser.find('.newdir form').on('beforeSubmit', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    
                    //$(this).attr('action', settings.route + 'createdir&token=' + settings.token + '&path=' + currentPath)
                    var data = $(this).serializeArray();
                    data.push({name: 'token', value: settings.token});
                    data.push({name: 'path', value: currentPath});
                    console.log(data);
                    
                    $.ajax( $(this).action, {
                        type: 'get',
                        data: data
                    }).done(function(data){
                         if(data.success){
                                window.location.reload();
                            } else {
                                alert(data.message);
                            }
                    });
                    return false;
                })


		// Navigates to the given hash (path)

		function goto(hash) {

			hash = decodeURIComponent(hash).slice(1).split('=');

			if (hash.length) {
				var rendered = '';

				// if hash has search in it

				if (hash[0] === 'search') {

					filebrowser.addClass('searching');
					rendered = searchData(response, hash[1].toLowerCase());

					if (rendered.length) {
						currentPath = hash[0];
						render(rendered);
					}
					else {
						render(rendered);
					}

				}

				// if hash is some path

				else if (hash[0].trim().length) {

					rendered = searchByPath(hash[0]);

					if (rendered.length) {

						currentPath = hash[0];
						breadcrumbsUrls = generateBreadcrumbs(hash[0]);
						render(rendered);

					}
					else {
						currentPath = hash[0];
						breadcrumbsUrls = generateBreadcrumbs(hash[0]);
						render(rendered);
					}

				}

				// if there is no hash

				else {
					currentPath = data.path;
					breadcrumbsUrls.push(data.path);
					render(searchByPath(data.path));
				}
			}
		}

		// Splits a file path and turns it into clickable breadcrumbs

		function generateBreadcrumbs(nextDir){
			var path = (nextDir).split('/').slice(0);
			for(var i=1;i<path.length;i++){
				path[i] = path[i-1]+ '/' +path[i];
			}
			return path;
		}


		// Locates a file by path

		function searchByPath(dir) {
			var path = dir.split('/'),
                            demo = response,
                            flag = 0;

			for(var i=0;i<path.length;i++){
				for(var j=0;j<demo.length;j++){
					if(demo[j].name === path[i]){
						flag = 1;
						demo = demo[j].items;
						break;
					}
				}
			}

			demo = flag ? demo : [];
			return demo;
		}


		// Recursively search through the file tree

		function searchData(data, searchTerms) {

			data.forEach(function(d){
				if(d.type === 'folder') {

					searchData(d.items,searchTerms);

					if(d.name.toLowerCase().match(searchTerms)) {
						folders.push(d);
					}
				}
				else if(d.type === 'file') {
					if(d.name.toLowerCase().match(searchTerms)) {
						files.push(d);
					}
				}
			});
			return {folders: folders, files: files};
		}


		// Render the HTML for the file manager

		function render(data) {

			var scannedFolders = [],
				scannedFiles = [];

			if(Array.isArray(data)) {

				data.forEach(function (d) {

					if (d.type === 'folder') {
						scannedFolders.push(d);
					}
					else if (d.type === 'file') {
						scannedFiles.push(d);
					}

				});

			}
			else if(typeof data === 'object') {

				scannedFolders = data.folders;
				scannedFiles = data.files;

			}


			// Empty the old result and make the new one

			fileList.empty().hide();

			if(!scannedFolders.length && !scannedFiles.length) {
				filebrowser.find('.nothingfound').show();
			}
			else {
				filebrowser.find('.nothingfound').hide();
			}

			if(scannedFolders.length) {
                            var folderTemplate = filebrowser.find('#folder-tpl').html();
				scannedFolders.forEach(function(f) {
                                        
                                        var item = $(folderTemplate);
                                        
					var itemsLength = f.items.length,
						name = escapeHTML(f.name);

					if(itemsLength) {
                                            item.find('span.icon').addClass('full');
					}

					if(itemsLength == 1) {
						itemsLength += ' item';
					}
					else if(itemsLength > 1) {
						itemsLength += ' items';
					}
					else {
						itemsLength = 'Empty';
					}
                                        item.find('a').attr({title:f.path, href:f.path});
                                        item.find('.name').html(name);
                                        item.find('.details').html(itemsLength);
                                    
                                        if(!settings.permissions.delete){
                                            item.find('button.delete').remove();
                                        }
                                        
					item.appendTo(fileList);
				});

			}

			if(scannedFiles.length) {
                                var fileTemplate = filebrowser.find('#file-tpl').html();
                                
				scannedFiles.forEach(function(f) {
                                    var fileSize = bytesToSize(f.size),
                                            name = escapeHTML(f.name),
                                            fileType = name.split('.');
                                            //icon = '<span class="icon file"></span>';

                                    fileType = fileType[fileType.length-1];

                                    var item = $(fileTemplate);

                                    item.find('span.icon').addClass('f-'+fileType).html(fileType);
                                    item.find('a').attr({title:f.path, href:f.path});
                                    item.find('.name').html(name);
                                    item.find('.details').html(fileSize);
                                    
                                    if(!settings.permissions.delete){
                                        item.find('button.delete').remove();
                                    }
                                    
                                    item.appendTo(fileList);
				});

			}


			// Generate the breadcrumbs

			var url = '';

			if(filebrowser.hasClass('searching')){

				url = '<span>Search results: </span>';
				fileList.removeClass('animated');

			}
			else {

				fileList.addClass('animated');

				breadcrumbsUrls.forEach(function (u, i) {

					var name = u.split('/');

					if (i !== breadcrumbsUrls.length - 1) {
						url += '<a href="'+u+'"><span class="folderName">' + name[name.length-1] + '</span></a> <span class="arrow">→</span> ';
					}
					else {
						url += '<span class="folderName">' + name[name.length-1] + '</span>';
					}

				});

			}

			breadcrumbs.text('').append(url);


			// Show the generated elements

			fileList.show().animate({'display':'inline-block'});

		}


		// This function escapes special html characters in names

		function escapeHTML(text) {
			return text.replace(/\&/g,'&amp;').replace(/\</g,'&lt;').replace(/\>/g,'&gt;');
		}


		// Convert file sizes from bytes to human readable units

		function bytesToSize(bytes) {
			var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
			if (bytes == 0) return '0 Bytes';
			var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
			return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
		}

	});
        

        return this;
    };

}(jQuery));


