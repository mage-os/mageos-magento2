/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */

/*eslint max-nested-callbacks: 0*/

define([
    'jquery',
    'squire'
], function ($, Squire) {
    'use strict';

    describe('Magento_Ui/js/form/element/file-uploader', function () {
        var injector = new Squire(),
            mocks = {
                'Magento_Ui/js/lib/core/events': {
                    on: jasmine.createSpy()
                },
                'Magento_Ui/js/lib/registry/registry': {
                    /** Method stub. */
                    get: function () {
                        return {
                            get: jasmine.createSpy(),
                            set: jasmine.createSpy()
                        };
                    },
                    create: jasmine.createSpy(),
                    set: jasmine.createSpy(),
                    async: jasmine.createSpy()
                },
                '/mage/utils/wrapper': jasmine.createSpy()
            },
            component,
            dataScope = 'dataScope',
            originalJQuery = jQuery.fn,
            params = {
                provider: 'provName',
                name: '',
                index: '',
                dataScope: dataScope
            };

        beforeEach(function (done) {
            injector.mock(mocks);
            injector.require([
                'Magento_Ui/js/form/element/file-uploader',
                'knockoutjs/knockout-es5'
            ], function (Constr) {
                component = new Constr(params);

                done();
            });
        });

        afterEach(function () {
            jQuery.fn = originalJQuery;
        });

        describe('initUploader method', function () {
            let uppyMock;

            beforeEach(function () {
                uppyMock = {
                    use: jasmine.createSpy('uppy.use'),
                    on: jasmine.createSpy('uppy.on'),
                    fileInput: jasmine.createSpyObj('fileInput', ['closest']),
                    Dashboard: jasmine.createSpy('Dashboard'),
                    DropTarget: jasmine.createSpy('DropTarget'),
                    XHRUpload: jasmine.createSpy('XHRUpload')
                };

                window.Uppy = { Uppy: function () { return uppyMock; } };
            });

            it('creates instance of file uploader', function () {
                let fileInputMock = document.createElement('input');

                spyOn(component, 'initUploader').and.callThrough();
                spyOn(component, 'replaceInputTypeFile');

                component.initUploader(fileInputMock);

                expect(component.initUploader).toHaveBeenCalledWith(fileInputMock);
                expect(component.replaceInputTypeFile).toHaveBeenCalledWith(fileInputMock);

                expect(uppyMock.use).toHaveBeenCalledWith(window.Uppy.Dashboard, jasmine.any(Object));
                expect(uppyMock.use).toHaveBeenCalledWith(window.Uppy.DropTarget, jasmine.any(Object));
                expect(uppyMock.use).toHaveBeenCalledWith(window.Uppy.XHRUpload, jasmine.any(Object));
            });
        });

        describe('setInitialValue method', function () {

            it('check for chainable', function () {
                expect(component.setInitialValue()).toEqual(component);
            });
            it('check for set value', function () {
                var initialValue = [
                        {
                            'name': 'test.png',
                            'size': 0,
                            'type': 'image/png',
                            'url': 'http://localhost:8000/media/wysiwyg/test.png'
                        }
                    ], expectedValue = [
                        {
                            'name': 'test.png',
                            'size': 2000,
                            'type': 'image/png',
                            'url': 'http://localhost:8000/media/wysiwyg/test.png'
                        }
                    ];

                spyOn(component, 'setImageSize').and.callFake(function () {
                    component.value().size = 2000;
                });
                spyOn(component, 'getInitialValue').and.returnValue(initialValue);
                component.service = true;
                expect(component.setInitialValue()).toEqual(component);
                expect(component.getInitialValue).toHaveBeenCalled();
                component.setImageSize(initialValue);
                expect(component.value().size).toEqual(expectedValue[0].size);
            });
        });

        describe('isFileAllowed method', function () {
            var invalidFile,
                validFile;

            invalidFile = {
                size: 2000,
                name: 'name.txt'
            };

            validFile = {
                size: 500,
                name: 'name.jpg'
            };

            it('validates file extension', function () {
                var valid,
                    invalid;

                component.allowedExtensions = ['jpg'];
                component.maxFileSize = false;

                valid   = component.isFileAllowed(validFile);
                invalid = component.isFileAllowed(invalidFile);

                expect(valid.passed).toBe(true);
                expect(invalid.passed).toBe(false);
            });

            it('validates file size', function () {
                var valid,
                    invalid;

                component.allowedExtensions = [];
                component.maxFileSize = 1000;

                valid = component.isFileAllowed(validFile);
                invalid = component.isFileAllowed(invalidFile);

                expect(valid.passed).toBe(true);
                expect(invalid.passed).toBe(false);
            });
        });

        describe('formatSize method', function () {
            it('converts bytes value to a more readable string representation', function () {
                var bytes       = 28912,
                    expected    = '28 KB',
                    result      = component.formatSize(bytes);

                expect(result).toEqual(expected);
            });
        });

        describe('reset method', function () {
            it('restores initial files set', function () {
                var file1 = {},
                    file2 = {};

                component.initialValue = [file1];

                component.addFile(file2);
                component.reset();

                expect(component.value()).toEqual(jasmine.arrayContaining([file1]));
                expect(component.value()).not.toEqual(jasmine.arrayContaining([file2]));
            });
        });

        describe('hasChanged method', function () {
            it('checks if files set is different from its initial value', function () {
                component.initialValue = [{}];

                component.addFile({});

                expect(component.hasChanged()).toBe(true);

                component.reset();

                expect(component.hasChanged()).toBe(false);
            });
        });

        describe('clear method', function () {
            it('removes all files from collection', function () {
                var file = {};

                component.addFile(file);

                expect(component.value().length).toBeGreaterThan(0);

                component.clear();

                expect(component.value().length).toEqual(0);
            });

            it('returns instance of component', function () {
                var instance = component.clear();

                expect(instance).toEqual(component);
            });
        });

        describe('addFile method', function () {
            it('adds single file to collection', function () {
                var file1 = {},
                    file2 = {};

                this.isMultipleFiles = false;

                component.addFile(file1);
                component.addFile(file2);

                expect(component.value()).toEqual(jasmine.arrayContaining([file2]));
                expect(component.value().length).toEqual(1);
            });

            it('adds multiple files to collection', function () {
                var file1 = {},
                    file2 = {};

                this.isMultipleFiles = true;

                component.addFile(file1);
                component.addFile(file2);

                expect(component.value()).toEqual(jasmine.arrayContaining([file1, file2]));

                this.isMultipleFiles = false;
            });

            it('returns instance of component', function () {
                var instance = component.addFile({});

                expect(instance).toEqual(component);
            });
        });

        describe('removeFile method', function () {
            it('removes single file from collection', function () {
                var file = {};

                component.addFile(file);
                component.removeFile(file);

                expect(component.value()).not.toEqual(jasmine.arrayContaining([file]));
            });

            it('returns instance of component', function () {
                var instance = component.removeFile({});

                expect(instance).toEqual(component);
            });
        });

        describe('getFile method', function () {
            it('returns instance of a file found by search criteria', function () {
                var matchedFile,
                    file = {};

                component.addFile(file);

                matchedFile = component.getFile(function (item) {
                    return item === file;
                });

                expect(matchedFile).toEqual(file);
            });
        });

        describe('hasData method', function () {
            it('checks that collection has some items', function () {
                var file = {};

                component.addFile(file);

                expect(component.hasData()).toBe(true);

                component.clear();

                expect(component.hasData()).toBe(false);
            });
        });

        describe('onLoadingStart method', function () {
            it('sets isLoading flag to be true', function () {
                component.isLoading = false;
                component.onLoadingStart();

                expect(component.isLoading).toBe(true);
            });
        });

        describe('onLoadingStop method', function () {
            it('drops isLoading flag', function () {
                component.isLoading = true;
                component.onLoadingStop();

                expect(component.isLoading).toBe(false);
            });
        });

        describe('onFileUploaded handler', function () {
            it('calls addFile method if upload was successful', function () {
                spyOn(component, 'aggregateError');
                spyOn(component, 'addFile');

                component.onFileUploaded({}, {
                    files: [{
                        name: 'hello.jpg'
                    }],
                    result: {
                        error: false
                    }
                });

                expect(component.aggregateError).not.toHaveBeenCalled();
                expect(component.addFile).toHaveBeenCalled();
            });

            it('should call uploaderConfig.stop when number of errors is equal to number of files', function () {
                var fakeEvent = {
                        target: document.createElement('input')
                    },
                    file = {
                        name: 'hello.jpg'
                    },
                    data = {
                        files: [file],
                        originalFiles: [file]
                    };

                spyOn(component, 'isFileAllowed').and.callFake(function (fileArg) {
                    expect(fileArg).toBe(file);

                    return {
                        passed: false,
                        message: 'Not awesome enough'
                    };
                });
                component.initUploader();
                spyOn(component.uploaderConfig, 'done');
                spyOn(component.uploaderConfig, 'stop');
                component.onBeforeFileUpload(fakeEvent, data);
                expect(component.uploaderConfig.stop).toHaveBeenCalled();
            });
            it('should not call uploaderConfig.stop when number of errors is unequal to number of files', function () {
                var fakeEvent = {
                        target: document.createElement('input')
                    },
                    file = {
                        name: 'hello.jpg'
                    },
                    otherFileInQueue = {
                        name: 'world.png'
                    },
                    data = {
                        files: [file],
                        originalFiles: [file, otherFileInQueue]
                    };

                component.initUploader();
                spyOn(component.uploaderConfig, 'done');
                spyOn(component.uploaderConfig, 'stop');
                spyOn(component, 'isFileAllowed').and.callFake(function (fileArg) {
                    expect(fileArg).toBe(file);

                    return {
                        passed: false,
                        message: 'Not awesome enough'
                    };
                });

                component.onBeforeFileUpload(fakeEvent, data);
                expect(component.uploaderConfig.stop).not.toHaveBeenCalled();
            });
        });

        describe('onElementRender handler', function () {
            it('invokes initUploader and bindFileBrowserTriggers methods', function () {
                let input = document.createElement('input');

                input.id = 'test-file-input';
                input.name = 'test-file-name';

                const $dropZone = $('<div data-role="drop-zone"></div>'),
                    $fileUploaderArea = $('<div class="file-uploader-area" upload-area-id="' + input.id + '">' +
                        '<button class="file-uploader-button"></button></div>'),
                    $placeholder = $('<div class="file-uploader-placeholder"></div>'),
                    button = $fileUploaderArea.find('.file-uploader-button')[0],
                    clickEvent = new MouseEvent('click', {bubbles: true, cancelable: true}),
                    placeholder = $placeholder[0],
                    clickEvent2 = new MouseEvent('click', {bubbles: true, cancelable: true});

                $dropZone.append($fileUploaderArea);
                $dropZone.append($placeholder);
                $('body').append($dropZone);

                spyOn(component, 'initUploader');
                spyOn(component, 'bindFileBrowserTriggers').and.callThrough();
                spyOn(component, 'triggerFileBrowser');

                component.onElementRender(input);

                expect(component.initUploader).toHaveBeenCalledWith(input);
                expect(component.bindFileBrowserTriggers).toHaveBeenCalledWith(input.id);

                button.dispatchEvent(clickEvent);
                expect(component.triggerFileBrowser).toHaveBeenCalled();

                // eslint-disable-next-line one-var
                const arg1 = component.triggerFileBrowser.calls.first().args[0];

                expect(arg1[0]).toBe($fileUploaderArea[0]);

                placeholder.dispatchEvent(clickEvent2);
                expect(component.triggerFileBrowser.calls.count()).toBe(2);

                // eslint-disable-next-line one-var
                const arg2 = component.triggerFileBrowser.calls.mostRecent().args[0];

                expect(arg2[0]).toBe($fileUploaderArea[0]);

                $dropZone.remove();
            });
        });

        describe('onFail handler', function () {
            it('it logs responseText and status', function () {
                var fakeEvent = {
                        target: document.createElement('input')
                    },
                    data = {
                        jqXHR: {
                            responseText: 'Failed',
                            status: '500'
                        }
                    };

                spyOn(console, 'error');

                component.onFail(fakeEvent, data);
                expect(console.error).toHaveBeenCalledWith(data.jqXHR.responseText);
                expect(console.error).toHaveBeenCalledWith(data.jqXHR.status);
                expect(console.error).toHaveBeenCalledTimes(2);
            });
        });

        describe('aggregateError method', function () {
            it('should append onto aggregatedErrors array when called', function () {
                spyOn(component.aggregatedErrors, 'push');

                component.aggregateError('blah.jpg', 'File is too awesome');

                expect(component.aggregatedErrors.push).toHaveBeenCalledWith({
                    filename: 'blah.jpg',
                    message: 'File is too awesome'
                });
            });
        });
    });
});
