LC.companyStructure = {
    init: function () {
        this.bindDragAndDrop();
        this.bindActionsMenu();
    },

    bindActionsMenu: function () {
        // Toggle del menú de acciones en mobile
        $(document).on('click', '.actions-menu-toggle', this.toggleActionsMenu.bind(this));

        // Cerrar menú al hacer click fuera
        $(document).on('click', function (event) {
            if (!$(event.target).closest('.userCompanyStructureActions').length) {
                $('.actions-dropdown-menu.show').removeClass('show');
            }
        });

        // Cerrar menú después de seleccionar una acción
        $(document).on('click', '.action-dropdown-item', function () {
            $(this).closest('.actions-dropdown-menu').removeClass('show');
        });
    },

    toggleActionsMenu: function (event) {
        event.stopPropagation();
        var $toggle = $(event.currentTarget);
        var nodeId = $toggle.data('node-id');
        var $menu = $('#actionsMenu' + nodeId);

        // Cerrar otros menús abiertos
        $('.actions-dropdown-menu.show').not($menu).removeClass('show');

        // Toggle del menú actual
        $menu.toggleClass('show');
    },

    bindDragAndDrop: function () {
        // Bind drag and drop events for company structure (desktop)
        $(document).on('dragstart', '.userCompanyStructureDragHandle[draggable="true"]', this.handleDragStart.bind(this));
        $(document).on('drag', '.userCompanyStructureDragHandle[draggable="true"]', this.handleDrag.bind(this));
        $(document).on('dragover', '.userCompanyStructureNodeHeader', this.handleDragOver.bind(this));
        $(document).on('dragleave', '.userCompanyStructureNodeHeader', this.handleDragLeave.bind(this));
        $(document).on('drop', '.userCompanyStructureNodeHeader', this.handleDrop.bind(this));
        $(document).on('dragend', '.userCompanyStructureDragHandle[draggable="true"]', this.handleDragEnd.bind(this));

        // Touch events para dispositivos móviles
        $(document).on('touchstart', '.userCompanyStructureDragHandle', this.handleTouchStart.bind(this));
        $(document).on('touchmove', '.userCompanyStructureDragHandle', this.handleTouchMove.bind(this));
        $(document).on('touchend', '.userCompanyStructureDragHandle', this.handleTouchEnd.bind(this));
        $(document).on('touchcancel', '.userCompanyStructureDragHandle', this.handleTouchCancel.bind(this));
    },

    /**
     * Check if a node is within the used account branch
     */
    isInUsedBranch: function ($node) {
        // Check if node itself is used account
        if ($node.hasClass('companyStructureUsed')) {
            return true;
        }
        // Check if any parent is the used account
        var $parent = $node.parent().closest('.userCompanyStructureNode');
        while ($parent.length) {
            if ($parent.hasClass('companyStructureUsed')) {
                return true;
            }
            $parent = $parent.parent().closest('.userCompanyStructureNode');
        }
        return false;
    },

    /**
     * Check if targetNode is a descendant of sourceNode (would create a cycle)
     */
    isDescendantOf: function (targetNodeId, sourceNodeId) {
        var $target = $('#companyNode' + targetNodeId);
        var $parent = $target.parent().closest('.userCompanyStructureNode');

        while ($parent.length) {
            var parentId = $parent.data('node-id');
            if (parentId == sourceNodeId) {
                return true; // Target is a child of source - would create cycle
            }
            $parent = $parent.parent().closest('.userCompanyStructureNode');
        }
        return false;
    },

    /**
     * Check if drop is allowed based on business rules
     */
    isDropAllowed: function ($draggedNode, $targetNode) {
        var draggedNodeId = $draggedNode.data('node-id');
        var targetNodeId = $targetNode.data('node-id');

        // Can't drop on itself
        if (draggedNodeId == targetNodeId) {
            return false;
        }

        // Can't drop a node as child of its own descendant (would create cycle)
        if (this.isDescendantOf(targetNodeId, draggedNodeId)) {
            return false;
        }

        // Check if target is already the current parent (no change would happen)
        var $currentParent = $draggedNode.parent().closest('.userCompanyStructureNode');
        if ($currentParent.length && $currentParent.data('node-id') == targetNodeId) {
            return false; // Already under this parent, no point in moving
        }

        // Check if dragged node is in used branch
        var draggedInUsedBranch = this.isInUsedBranch($draggedNode);

        // Check if target is in used branch
        var targetInUsedBranch = this.isInUsedBranch($targetNode);

        // Rule: Can't move nodes from outside used branch into it
        if (!draggedInUsedBranch && targetInUsedBranch) {
            return false;
        }

        // Rule: Can't move nodes from inside used branch to outside
        if (draggedInUsedBranch && !targetInUsedBranch) {
            return false;
        }

        // All checks passed
        return true;
    },

    handleDragStart: function (event) {
        var $dragHandle = $(event.currentTarget);
        var $node = $dragHandle.closest('.userCompanyStructureNode');
        var nodeId = $node.data('node-id');
        var nodeName = $node.find('.companyStructureNodeTitle').first().text().trim();

        event.originalEvent.dataTransfer.setData('text/plain', nodeId);
        event.originalEvent.dataTransfer.effectAllowed = 'move';

        // Hide default drag image (create invisible element)
        var dragImage = document.createElement('div');
        dragImage.style.opacity = '0';
        dragImage.style.position = 'absolute';
        dragImage.style.top = '-9999px';
        document.body.appendChild(dragImage);
        event.originalEvent.dataTransfer.setDragImage(dragImage, 0, 0);

        // Remove invisible element after a short delay
        setTimeout(function () {
            document.body.removeChild(dragImage);
        }, 0);

        // Apply dragging class to node
        $node.addClass('dragging');

        // Store dragged info (including jQuery object for validation)
        this.draggedNodeId = nodeId;
        this.draggedNodeName = nodeName;
        this.$draggedNode = $node;

        // Show drag badge with translation
        var $badge = $('#companyStructureDragBadge');
        var movingLabel = LC.global.languageSheet.accountCompanyStructureMoving || 'Moving';
        $badge.text(movingLabel + ': ' + nodeName);
        $badge.addClass('active');
    },

    handleDrag: function (event) {
        // Update badge position to follow cursor (desktop)
        var $badge = $('#companyStructureDragBadge');
        if ($badge.hasClass('active')) {
            $badge.css({
                left: event.pageX + 15 + 'px',
                top: event.pageY + 15 + 'px'
            });
        }
    },

    handleDragOver: function (event) {
        event.preventDefault();
        event.stopPropagation();

        var $targetHeader = $(event.currentTarget);
        var $targetNode = $targetHeader.closest('.userCompanyStructureNode');
        var targetNodeId = $targetNode.data('node-id');

        // Check if drop is allowed using business rules
        if (!this.isDropAllowed(this.$draggedNode, $targetNode)) {
            event.originalEvent.dataTransfer.dropEffect = 'none';
            // Remove any existing drop indicators
            if (this.currentDropTarget === targetNodeId) {
                $('.userCompanyStructureNode').removeClass('drop-target');
                $('.userCompanyStructureNode').removeAttr('data-drop-message');
                this.currentDropTarget = null;
            }
            return;
        }

        event.originalEvent.dataTransfer.dropEffect = 'move';

        // Only update if this is a different target than current
        if (this.currentDropTarget !== targetNodeId) {
            // Remove drop-target from all nodes first
            $('.userCompanyStructureNode').removeClass('drop-target');
            $('.userCompanyStructureNode').removeAttr('data-drop-message');

            // Add to current target with custom message using translation
            var targetNodeName = $targetNode.find('.companyStructureNodeTitle').first().text().trim();
            $targetNode.addClass('drop-target');

            var dropTemplate = LC.global.languageSheet.accountCompanyStructureDropUnder || 'Drop "{{source}}" under "{{target}}"';
            var dropMessage = dropTemplate.replace('{{source}}', this.draggedNodeName).replace('{{target}}', targetNodeName);
            $targetNode.attr('data-drop-message', dropMessage);

            this.currentDropTarget = targetNodeId;
        }
    },

    handleDragLeave: function (event) {
        // Only remove if we're leaving the node completely (not just entering a child)
        var $targetHeader = $(event.currentTarget);
        var $targetNode = $targetHeader.closest('.userCompanyStructureNode');
        var relatedTarget = event.originalEvent.relatedTarget;

        // Check if we're moving to a child element
        if (relatedTarget && $targetNode[0].contains(relatedTarget)) {
            return; // Don't remove, we're still inside the node
        }

        // Only remove if this was the current drop target
        if (this.currentDropTarget === $targetNode.data('node-id')) {
            $targetNode.removeClass('drop-target');
            $targetNode.removeAttr('data-drop-message');
            this.currentDropTarget = null;
        }
    },

    handleDrop: function (event) {
        event.preventDefault();
        event.stopPropagation();

        var $targetHeader = $(event.currentTarget);
        var $targetNode = $targetHeader.closest('.userCompanyStructureNode');
        var targetNodeId = $targetNode.data('node-id');
        var draggedNodeId = event.originalEvent.dataTransfer.getData('text/plain');

        $('.drop-target').removeClass('drop-target');
        $('.userCompanyStructureNode').removeAttr('data-drop-message');
        this.currentDropTarget = null;

        // Final validation before drop
        if (!this.isDropAllowed(this.$draggedNode, $targetNode)) {
            LC.notify('No se puede mover esta división aquí', { type: 'danger' });
            return;
        }

        // Prepare data for account update
        var updateData = {
            accountId: draggedNodeId,
            parentId: targetNodeId
        };

        // Use UPDATE_ACCOUNT route
        $.post(
            LC.global.routePaths.ACCOUNT_INTERNAL_UPDATE_ACCOUNT,
            { data: JSON.stringify(updateData) },
            function (response) {
                if (response && response.data && response.data.response) {
                    if (response.data.response.success == 1) {
                        LC.notify(response.data.response.message || 'Division moved successfully', { type: 'success' });
                        // Reload to refresh the structure
                        location.reload();
                    } else {
                        LC.notify(response.data.response.message || 'Error moving division', { type: 'danger' });
                    }
                } else {
                    LC.notify('Error moving division', { type: 'danger' });
                }
            },
            'json'
        ).fail(function () {
            LC.notify('Error moving division', { type: 'danger' });
        });
    },

    handleDragEnd: function () {
        $('.dragging').removeClass('dragging');
        $('.drop-target').removeClass('drop-target');
        $('.userCompanyStructureNode').removeAttr('data-drop-message');

        // Hide badge
        $('#companyStructureDragBadge').removeClass('active');

        this.draggedNodeId = null;
        this.draggedNodeName = null;
        this.$draggedNode = null;
        this.currentDropTarget = null;
    },

    // ===== TOUCH EVENTS PARA MOBILE =====

    handleTouchStart: function (event) {
        var $dragHandle = $(event.currentTarget);
        var $node = $dragHandle.closest('.userCompanyStructureNode');
        var nodeId = $node.data('node-id');
        var nodeName = $node.find('.companyStructureNodeTitle').first().text().trim();

        // Guardar información del touch
        this.touchActive = true;
        this.touchStartY = event.originalEvent.touches[0].pageY;
        this.touchStartX = event.originalEvent.touches[0].pageX;
        this.draggedNodeId = nodeId;
        this.draggedNodeName = nodeName;
        this.$draggedNode = $node;
        this.$draggedHandle = $dragHandle;

        // Feedback visual inmediato en mobile
        $dragHandle.addClass('drag-starting');

        // Delay reducido para mejor responsividad (150ms vs 200ms)
        this.touchStartTime = Date.now();
        this.touchDragTimeout = setTimeout(function () {
            if (this.touchActive) {
                // Quitar clase de inicio y añadir dragging
                $dragHandle.removeClass('drag-starting');
                $node.addClass('dragging');

                // Vibración háptica si está disponible (solo mobile)
                if (navigator.vibrate) {
                    navigator.vibrate(50);
                }

                // Mostrar badge
                var $badge = $('#companyStructureDragBadge');
                var movingLabel = LC.global.languageSheet.accountCompanyStructureMoving || 'Moving';
                $badge.text(movingLabel + ': ' + nodeName);
                $badge.addClass('active');
            }
        }.bind(this), 150);
    },

    handleTouchMove: function (event) {
        if (!this.touchActive || !this.$draggedNode) {
            return;
        }

        var touch = event.originalEvent.touches[0];
        var deltaY = Math.abs(touch.pageY - this.touchStartY);
        var deltaX = Math.abs(touch.pageX - this.touchStartX);

        // Si el movimiento es principalmente vertical (scroll), cancelar drag
        // Ajustado a 150ms para coincidir con el nuevo delay
        if (deltaY > 10 && deltaY > deltaX * 2 && Date.now() - this.touchStartTime < 150) {
            // Quitar clase de inicio si se cancela
            if (this.$draggedHandle) {
                this.$draggedHandle.removeClass('drag-starting');
            }
            this.handleTouchCancel();
            return;
        }

        // Si estamos dragging, prevenir scroll
        if (this.$draggedNode.hasClass('dragging')) {
            event.preventDefault();

            // Actualizar posición del badge - más cerca del dedo en mobile
            var $badge = $('#companyStructureDragBadge');
            // Posicionar arriba y ligeramente a la derecha para no tapar con el dedo
            $badge.css({
                left: touch.pageX + 10 + 'px',
                top: touch.pageY - 60 + 'px'  // Arriba del dedo
            });

            // Detectar elemento bajo el dedo
            var elementBelow = document.elementFromPoint(touch.clientX, touch.clientY);
            var $headerBelow = $(elementBelow).closest('.userCompanyStructureNodeHeader');

            if ($headerBelow.length) {
                var $targetNode = $headerBelow.closest('.userCompanyStructureNode');
                var targetNodeId = $targetNode.data('node-id');

                // Validar si el drop es permitido
                if (this.isDropAllowed(this.$draggedNode, $targetNode)) {
                    // Solo actualizar si es diferente al target actual
                    if (this.currentDropTarget !== targetNodeId) {
                        $('.userCompanyStructureNode').removeClass('drop-target');
                        $('.userCompanyStructureNode').removeAttr('data-drop-message');

                        var targetNodeName = $targetNode.find('.companyStructureNodeTitle').first().text().trim();
                        $targetNode.addClass('drop-target');

                        var dropTemplate = LC.global.languageSheet.accountCompanyStructureDropUnder || 'Drop "{{source}}" under "{{target}}"';
                        var dropMessage = dropTemplate.replace('{{source}}', this.draggedNodeName).replace('{{target}}', targetNodeName);
                        $targetNode.attr('data-drop-message', dropMessage);

                        this.currentDropTarget = targetNodeId;
                    }
                } else {
                    // Drop no permitido
                    if (this.currentDropTarget === targetNodeId) {
                        $targetNode.removeClass('drop-target');
                        $targetNode.removeAttr('data-drop-message');
                        this.currentDropTarget = null;
                    }
                }
            } else {
                // No hay header debajo
                if (this.currentDropTarget) {
                    $('.userCompanyStructureNode').removeClass('drop-target');
                    $('.userCompanyStructureNode').removeAttr('data-drop-message');
                    this.currentDropTarget = null;
                }
            }
        }
    },

    handleTouchEnd: function () {
        if (this.touchDragTimeout) {
            clearTimeout(this.touchDragTimeout);
            this.touchDragTimeout = null;
        }

        if (!this.touchActive) {
            return;
        }

        this.touchActive = false;

        // Si no estaba realmente dragging, no hacer nada
        if (!this.$draggedNode || !this.$draggedNode.hasClass('dragging')) {
            this.handleTouchCancel();
            return;
        }

        // Si hay un target válido, hacer el drop
        if (this.currentDropTarget) {
            var $targetNode = $('#companyNode' + this.currentDropTarget);

            // Validación final
            if (this.isDropAllowed(this.$draggedNode, $targetNode)) {
                var draggedNodeId = this.draggedNodeId;
                var targetNodeId = this.currentDropTarget;

                // Preparar datos para actualización
                var updateData = {
                    accountId: draggedNodeId,
                    parentId: targetNodeId
                };

                // Usar UPDATE_ACCOUNT route
                $.post(
                    LC.global.routePaths.ACCOUNT_INTERNAL_UPDATE_ACCOUNT,
                    { data: JSON.stringify(updateData) },
                    function (response) {
                        if (response && response.data && response.data.response) {
                            if (response.data.response.success == 1) {
                                LC.notify(response.data.response.message || 'Division moved successfully', { type: 'success' });
                                // Reload to refresh the structure
                                location.reload();
                            } else {
                                LC.notify(response.data.response.message || 'Error moving division', { type: 'danger' });
                            }
                        } else {
                            LC.notify('Error moving division', { type: 'danger' });
                        }
                    },
                    'json'
                ).fail(function () {
                    LC.notify('Error moving division', { type: 'danger' });
                });
            }
        }

        // Limpiar estado
        this.handleTouchCancel();
    },

    handleTouchCancel: function () {
        if (this.touchDragTimeout) {
            clearTimeout(this.touchDragTimeout);
            this.touchDragTimeout = null;
        }

        this.touchActive = false;
        this.touchStartY = null;
        this.touchStartX = null;
        this.touchStartTime = null;

        // Limpiar clases visuales
        $('.drag-starting').removeClass('drag-starting');
        $('.dragging').removeClass('dragging');
        $('.drop-target').removeClass('drop-target');
        $('.userCompanyStructureNode').removeAttr('data-drop-message');

        // Hide badge
        $('#companyStructureDragBadge').removeClass('active');

        this.draggedNodeId = null;
        this.draggedNodeName = null;
        this.$draggedNode = null;
        this.$draggedHandle = null;
        this.currentDropTarget = null;
    }
};

LC.initQueue.enqueue(function () {
    LC.companyStructure.init();
});
