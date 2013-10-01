<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace Thelia\Tests\Action;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Action\Folder;
use Thelia\Core\Event\Folder\FolderCreateEvent;
use Thelia\Core\Event\Folder\FolderDeleteEvent;
use Thelia\Core\Event\Folder\FolderUpdateEvent;
use Thelia\Model\FolderQuery;


/**
 * Class FolderTest
 * @package Thelia\Tests\Action\ImageTest
 * @author Manuel Raynaud <mraynaud@openstudio.fr>
 */
class FolderTest extends BaseAction
{
    /**
     * test folder creation
     * @covers Thelia\Action\Folder::create
     */
    public function testCreateFolder()
    {

        $event = new FolderCreateEvent();
        $event
            ->setParent(0)
            ->setVisible(1)
            ->setLocale('en_US')
            ->setTitle('folder creation test');

        $folderAction = new Folder($this->getContainer());

        $folderAction->create($event);

        $folder = $event->getFolder();

        $this->assertInstanceOf('Thelia\Model\Folder', $folder);
        $this->assertEquals('folder creation test', $folder->getTitle());
        $this->assertEquals(1, $folder->getVisible());
        $this->assertEquals(0, $folder->getParent());
    }

    /**
     * test update creation
     * @covers Thelia\Action\Folder::update
     */
    public function testUpdateFolder()
    {
        $folder = FolderQuery::create()
            ->addAscendingOrderByColumn('RAND()')
            ->findOne();

        if(null === $folder) {
            $this->fail('use fixtures before launching test, there is no folder in database');
        }

        $visible = !$folder->getVisible();
        $event = new FolderUpdateEvent($folder->getId());

        $event
            ->setLocale('en_US')
            ->setTitle('test update folder')
            ->setVisible($visible)
            ->setChapo('test folder update chapo')
            ->setDescription('update folder description')
            ->setPostscriptum('update folder postscriptum')
            ->setParent(0)
        ;

        $folderAction = new Folder($this->getContainer());
        $folderAction->update($event);

        $updatedFolder = $event->getFolder();

        $this->assertInstanceOf('Thelia\Model\Folder', $updatedFolder);
        $this->assertEquals('test update folder', $updatedFolder->getTitle());
        $this->assertEquals('test folder update chapo', $updatedFolder->getChapo());
        $this->assertEquals('update folder description', $updatedFolder->getDescription());
        $this->assertEquals('update folder postscriptum', $updatedFolder->getPostscriptum());
        $this->assertEquals(0, $updatedFolder->getParent());
        $this->assertEquals($visible, $updatedFolder->getVisible());
    }

    /**
     * test folder removal
     * @covers Thelia\Action\Folder::delete
     */
    public function testDeleteFolder()
    {
        $folder = FolderQuery::create()
            ->addAscendingOrderByColumn('RAND()')
            ->findOne();

        if(null === $folder) {
            $this->fail('use fixtures before launching test, there is no folder in database');
        }

        $event = new FolderDeleteEvent($folder->getId());

        $folderAction = new Folder($this->getContainer());
        $folderAction->delete($event);

        $deletedFolder = $event->getFolder();

        $this->assertInstanceOf('Thelia\Model\Folder', $deletedFolder);
        $this->assertTrue($deletedFolder->isDeleted());
    }
}