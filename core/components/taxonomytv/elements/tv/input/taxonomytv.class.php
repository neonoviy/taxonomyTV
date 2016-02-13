<?php
/**
 * @var modX $this->modx
 * @var modTemplateVar $this
 * 
 * @package modx
 * @subpackage processors.element.tv.renders.mgr.input
 */
class modTemplateVarInputRenderResourceList extends modTemplateVarInputRender {



    public function process($value,array $params = array()) {

//Эта функция определяет уровень вложенности папки в дереве.    
    function level(array $array, $parent_id, $level) {
    	if($parent_id == $params['parents']){return $level;}else{
    	foreach ($array as $value) {
    		if ($value[id] == $parent_id) {
    			$new_parent_id = $value[parent];
    			$level++;
    			
    			return level($array, $new_parent_id, $level);
    		}
    	}	
    	}
    }
    
        $parents = $this->getInputOptions();
        $parents = !empty($params['parents']) || $params['parents'] === '0' ? explode(',',$params['parents']) : $parents;
        $params['depth'] = !empty($params['depth']) ? $params['depth'] : 10;
        if (empty($parents) || (empty($parents[0]) && $parents[0] !== '0')) {
            $parents = array();
        }
        $parentList = array();
        foreach ($parents as $parent) {
            /** @var modResource $parent */
            $parent = $this->modx->getObject('modResource',$parent);
            if ($parent) $parentList[] = $parent;
        }

        /* get all children */
        $ids = array();
        if (!empty($parentList)) {
            foreach ($parentList as $parent) {
                if (!empty($params['includeParent'])) $ids[] = $parent->get('id');
                $children = $this->modx->getChildIds($parent->get('id'),$params['depth'],array(
                    'context' => $parent->get('context_key'),
                ));
                $ids = array_merge($ids,$children);
            }
            $ids = array_unique($ids);
        }

        $c = $this->modx->newQuery('modResource');
        $c->leftJoin('modResource','Parent');
        if (!empty($ids)) {
            $c->where(array('modResource.id:IN' => $ids));
        } else if (!empty($parents) && $parents[0] == 0) {
            $c->where(array('modResource.parent' => 0));
        }
        if (!empty($params['where'])) {
            $params['where'] = $this->modx->fromJSON($params['where']);
            $c->where($params['where']);
        }
    	if (!empty($params['limitRelatedContext']) && ($params['limitRelatedContext'] == 1 || $params['limitRelatedContext'] == 'true')) {
			$context_key = $this->modx->resource->get('context_key');
            $c->where(array('modResource.context_key' => $context_key));
		}
        $c->sortby('Parent.menuindex,modResource.menuindex','ASC');
        if (!empty($params['limit'])) {
            $c->limit($params['limit']);
        }
        $resources = $this->modx->getCollection('modResource',$c);

        /* iterate */
        $opts = array();
        if (!empty($params['showNone'])) {
            $opts[] = array('value' => '','text' => '-','selected' => $this->tv->get('value') == '');
        }
        /** @var modResource $resource */
        foreach ($resources as $resource) {
            $selectedvalue = $this->tv->get('value');
            $selected =  explode("||",$selectedvalue);
            
            if (in_array($resource->get('id'),$selected)) {
                $checked = true;
            }else {
            	$checked = false;
            }
            
            $folders[] = array(
                'value' => $resource->get('id'),
                'text' => $resource->get('pagetitle'),
                'parent' => $resource->get('parent'),
                'checked' => $checked,
            );
        }
//Всё, что выше взято из стандартного recourcelist (список ресурсов)
//В $folders полный список ресурсов, нужно сделать отдельно: папки и документы. Создаем массив со всеми родителями и убираем из него повторы        
        foreach ($folders as $item) {
        		$preallfolders[] = $item[parent];
        		$preallfolders = array_unique($preallfolders);
        }
//Сравниваем $folders и $preallfolders, в котором лежат уникальные родители. Если есть совпадение — перед нами папка, иначе документ.        
        foreach ($folders as $item) {
        	  if (in_array($item[value], $preallfolders)) {
        	  
        	  	$allfolders[] = array('id' => $item[value], 'name' => $item[text], 'checked' => $item[checked], 'parent' => $item[parent]);
        	  }else {
        	  	$opts[] = array(
        	  	                'text' => $item[text],
        	  	                'value' => $item[value],
        	  	                'checked' => $item[checked],
        	  	                'parent' => $item[parent]
        	  	); 
        	  }
        	        
        }
//У нас есть массив с папками, нужно добавить к каждой папке уровень вложенности и отсортировать массив, чтоб дети не обрабатывались раньше родителей в шаблоне.        
        foreach ($allfolders as $item) {
        $startlvl = 0;
        $level = level($allfolders, $item[parent], $startlvl);
        	$sortedfolders[] = array('level' => $level, 'id' => $item[id], 'name' => $item[name], 'checked' => $item[checked], 'parent' => $item[parent]);
        }        
asort($opts); 
asort($sortedfolders);               
        
//Передаем массив с папками $allfolders и массив с документами $opts в шаблон
                $this->setPlaceholder('cbdefaults',implode(',',$defaults));
                $this->setPlaceholder('opts',$opts);
                $this->setPlaceholder('allfolders',$sortedfolders);
        				$this->setPlaceholder('rootid',$params['parents']);
    				    $this->setPlaceholder('widthfix',$params['widthfix']);
       
    }
    public function getTemplate() {
        return $this->modx->getOption('core_path').'components/taxonomytv/elements/tv/input/tpl/taxonomytv.tpl';
    }
}
return 'modTemplateVarInputRenderResourceList';