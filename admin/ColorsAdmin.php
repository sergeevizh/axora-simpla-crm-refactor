<?PHP

namespace Axora;

use App\Api\Axora;

class ColorsAdmin extends Axora
{
    public function fetch()
    {

        // Обработка действий
        if ($this->request->method('post')) {

            // Действия с выбранными
            $ids = $this->request->post('check');

            if (is_array($ids))
                switch ($this->request->post('action')) {
                    case 'delete':
                        {
                            foreach ($ids as $id) {
                                $this->colors->delete_color($id);
                            }
                            break;
                        }
                }

            // Сортировка
            $positions = $this->request->post('positions');
            $ids = array_keys($positions);
            sort($positions);
            foreach ($positions as $i => $position) {
                $this->colors->update_color($ids[$i], array('position' => $i));
            }
            $this->cache->deleteAll();
        }

        $colors = $this->colors->get_colors();

        $this->design->assign('colors', $colors);
        return $this->design->fetch('colors.tpl');
    }
}

