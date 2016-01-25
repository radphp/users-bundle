<?php

namespace Users\Action;

use App\Action\AppAction;
use Cake\ORM\TableRegistry;
use Rad\Cryptography\Password\DefaultPassword;
use Rad\DependencyInjection\Container;
use Rad\Network\Http\Request\UploadedFile;
use Users\Domain\Entity\UserDetail;
use Users\Library\Form;

/**
 * Import Action
 *
 * @package Users\Action
 */
class ImportAction extends AppAction
{
    /**
     * @var bool
     */
    public $needsAuthentication = true;

    /**
     * Get method
     *
     * @throws \Rad\Core\Exception\BaseException
     */
    public function getMethod()
    {
        $this->getResponder()->setData('form', Form::create()->getImportForm());
    }

    public function postMethod()
    {
        $file = $this->getFile();
        $rows = $this->getCsvData($file);

        /**
         * @Todo Error handling:
         *       - check required columns existence
         *       - check validity of data
         *       - check data duplications
         *       - etc
         */

        /** @var \Cake\ORM\Table $usersTable */
        $usersTable = TableRegistry::get('Users.Users');
        foreach ($rows as $row) {
            $user = $usersTable->newEntity();

            $user->set('username', $row['username'])
                ->set('email', isset($row['email']) ? $row['email'] : '')
                ->set('status', isset($row['status']) ? $row['status'] : '')
                ->set('password', (new DefaultPassword())->hash($row['password']))
                ->set('roles', explode(',', $row['roles']))
                ->set('details',
                    [
                        new UserDetail([
                            'key' => 'first_name',
                            'value' => isset($row['first_name']) ? $row['first_name'] : ''
                        ]),
                        new UserDetail([
                            'key' => 'middle_name',
                            'value' => isset($row['middle_name']) ? $row['middle_name'] : ''
                        ]),
                        new UserDetail([
                            'key' => 'last_name',
                            'value' => isset($row['last_name']) ? $row['last_name'] : ''
                        ])
                    ]);

            $usersTable->save($user);
        }
    }

    protected function getFile()
    {
        $request = Container::get('request');

        /** @var UploadedFile $file */
        $file = $request->getUploadedFiles()['form']['csv_file'];
        return $file->getFile();
    }

    /**
     * Get csv data
     *
     * @param string      $filename Filename
     * @param string|null $column   Return one CSV column
     *
     * @return array
     */
    protected function getCsvData($filename, $column = null)
    {
        $csvData = [];

        if (($handle = fopen($filename, 'r')) !== false) {
            $headerRow = [];
            $rowNum = 0;
            while (($row = fgetcsv($handle, null, ',')) !== false) {
                if ($rowNum == 0) {
                    $headerRow = $row;
                    $rowNum++;
                    continue;
                }

                for ($index = 0; $index < count($headerRow); $index++) {
                    $csvData[$rowNum - 1][trim($headerRow[$index])] = trim($row[$index]);
                }

                $rowNum++;
            }

            fclose($handle);
        }

        if ($column) {
            return array_column($csvData, $column);
        }

        return $csvData;
    }
}
