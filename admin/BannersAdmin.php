<?php


namespace Axora;

use App\Api\Axora;

class BannersAdmin extends Axora
{
	public function fetch()
	{

		// Обработка действий
		if($this->request->method('post'))
		{
			// Действия с выбранными
			$ids = $this->request->post('check');

			if(is_array($ids))
				switch($this->request->post('action'))
				{
					case 'delete':
					{
						foreach($ids as $id)
							$this->banners->delete($id);
					break;
					}
				}

			// Сортировка
			$positions = $this->request->post('positions');
			$ids = array_keys($positions);
			sort($positions);
			foreach($positions as $i=>$position)
				$this->banners->update($ids[$i], array('position'=>$position));

		}
        $this->design->assign('types', $this->banners->types);
        $this->design->assign('typesNames', $this->banners->typesNames);


		$banners = $this->banners->gets();
		$this->design->assign('banners', $banners);



		return $this->design->fetch('banners.tpl');
	}
}
