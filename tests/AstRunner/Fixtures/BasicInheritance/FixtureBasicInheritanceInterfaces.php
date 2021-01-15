<?php 

namespace Tests\SensioLabs\Deptrac\AstRunner\Fixtures;

interface FixtureBasicInheritanceInterfaceA { }
interface FixtureBasicInheritanceInterfaceB extends FixtureBasicInheritanceInterfaceA { }
interface FixtureBasicInheritanceInterfaceC extends FixtureBasicInheritanceInterfaceB { }
interface FixtureBasicInheritanceInterfaceD extends FixtureBasicInheritanceInterfaceC { }
interface FixtureBasicInheritanceInterfaceE extends FixtureBasicInheritanceInterfaceD { }
