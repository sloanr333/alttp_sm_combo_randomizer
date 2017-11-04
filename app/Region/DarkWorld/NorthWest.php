<?php namespace ALttP\Region\DarkWorld;

use ALttP\Item;
use ALttP\Location;
use ALttP\Region;
use ALttP\Support\LocationCollection;
use ALttP\World;

/**
 * North West Dark World Region and it's Locations contained within
 */
class NorthWest extends Region {
	protected $name = 'Dark World';

	/**
	 * Create a new North West Dark World Region and initalize it's locations
	 *
	 * @param World $world World this Region is part of
	 *
	 * @return void
	 */
	public function __construct(World $world) {
		parent::__construct($world);

		$this->locations = new LocationCollection([
			new Location\Chest("Brewery", 0xE9EC, null, $this),
			new Location\Chest("C-Shaped House", 0xE9EF, null, $this),
			new Location\Chest("Chest Game", 0xEDA8, null, $this),
			new Location\Standing("Hammer Pegs", 0x180006, null, $this),
			new Location\Standing("Bumper Cave", 0x180146, null, $this),
			new Location\Npc("Blacksmith", $world->config('region.swordsInPool', true) ? 0x18002A : 0x3355C, null, $this),
			new Location\Npc("Purple Chest", 0x33D68, null, $this),
		]);
	}

	/**
	 * Set Locations to have Items like the vanilla game.
	 *
	 * @return $this
	 */
	public function setVanilla() {
		$this->locations["Brewery"]->setItem(Item::get('RedBoomerang'));
		$this->locations["C-Shaped House"]->setItem(Item::get('ThreeHundredRupees'));
		$this->locations["Chest Game"]->setItem(Item::get('PieceOfHeart'));
		$this->locations["Hammer Pegs"]->setItem(Item::get('PieceOfHeart'));
		$this->locations["Bumper Cave"]->setItem(Item::get('PieceOfHeart'));
		$this->locations["Blacksmith"]->setItem(Item::get('L3Sword'));
		$this->locations["Purple Chest"]->setItem(Item::get('Bottle'));

		return $this;
	}

	/**
	 * Initalize the requirements for Entry and Completetion of the Region as well as access to all Locations contained
	 * within for No Major Glitches
	 *
	 * @return $this
	 */
	public function initNoMajorGlitches() {
		$this->locations["Hammer Pegs"]->setRequirements(function($locations, $items) {
			return $items->canLiftDarkRocks() && $items->has('Hammer');
		});

		$this->locations["Bumper Cave"]->setRequirements(function($locations, $items) {
			return $items->canLiftRocks() && $items->has('Cape');
		});

		$this->locations["Blacksmith"]->setRequirements(function($locations, $items) {
			return $items->canLiftDarkRocks();
		});

		$this->locations["Purple Chest"]->setRequirements(function($locations, $items) {
			return $items->canLiftDarkRocks();
		});

		$this->can_enter = function($locations, $items) {
			return $items->has('MoonPearl')
				&& (($this->world->getRegion('North East Dark World')->canEnter($locations, $items)
					&& ($items->has('Hookshot') && ($items->has('Flippers') || $items->canLiftRocks() || $items->has('Hammer'))))
					|| ($items->has('Hammer') && $items->canLiftRocks())
					|| $items->canLiftDarkRocks());
		};

		return $this;
	}

	/**
	 * Initalize the requirements for Entry and Completetion of the Region as well as access to all Locations contained
	 * within for MajorGlitches Mode
	 *
	 * @return $this
	 */
	public function initMajorGlitches() {
		$this->initOverworldGlitches();

		$this->locations["Brewery"]->setRequirements(function($locations, $items) {
			return $items->glitchedLinkInDarkWorld();
		});

		$this->locations["Hammer Pegs"]->setRequirements(function($locations, $items) {
			return $items->has('Hammer') && $items->glitchedLinkInDarkWorld();
		});

		$this->locations["Bumper Cave"]->setRequirements(function($locations, $items) {
			return $items->glitchedLinkInDarkWorld()
				&& ($items->has('PegasusBoots')
						|| ($items->canLiftRocks() && $items->has('Cape')));
		});

		$this->locations["Blacksmith"]->setRequirements(function($locations, $items) {
			return $items->glitchedLinkInDarkWorld()
				&& ($items->canLiftDarkRocks()
					|| ($items->has('PegasusBoots') && $items->has('MagicMirror')));
		});

		$this->locations["Purple Chest"]->setRequirements(function($locations, $items) {
			return $locations["Blacksmith"]->canAccess($items)
				&& ($items->has('MagicMirror')
					|| ($items->glitchedLinkInDarkWorld() && $items->canLiftDarkRocks())
					|| ($items->has('PegasusBoots') && $items->glitchedLinkInDarkWorld()
						&& $this->world->getRegion('North East Dark World')->canEnter($locations, $items)));
		});

		$this->can_enter = function($locations, $items) {
			return ($items->has('MoonPearl')
					&& ($items->canLiftDarkRocks()
						|| ($items->has('Hammer') && $items->canLiftRocks())
						|| ($items->has('DefeatAgahnim') && $items->has('Hookshot')
								&& ($items->has('Hammer') || $items->canLiftRocks() || $items->has('Flippers')))))
				|| $this->world->getRegion('West Death Mountain')->canEnter($locations, $items);
		};

		return $this;
	}

	/**
	 * Initalize the requirements for Entry and Completetion of the Region as well as access to all Locations contained
	 * within for Overworld Glitches Mode
	 *
	 * @return $this
	 */
	public function initOverworldGlitches() {
		$this->locations["Brewery"]->setRequirements(function($locations, $items) {
			return $items->has('MoonPearl');
		});

		$this->locations["Hammer Pegs"]->setRequirements(function($locations, $items) {
			return $items->has('Hammer') && $items->has('MoonPearl')
				&& ($items->canLiftDarkRocks()
					|| ($items->has('PegasusBoots')
						&& $this->world->getRegion('North East Dark World')->canEnter($locations, $items)));
		});

		$this->locations["Bumper Cave"]->setRequirements(function($locations, $items) {
			return $items->has('MoonPearl')
				&& ($items->has('PegasusBoots')
						|| ($items->canLiftRocks() && $items->has('Cape')));
		});

		$this->locations["Blacksmith"]->setRequirements(function($locations, $items) {
			return $items->has('MoonPearl')
				&& ($items->canLiftDarkRocks()
					|| ($items->has('PegasusBoots') && $items->has('MagicMirror')));
		});

		$this->locations["Purple Chest"]->setRequirements(function($locations, $items) {
			return $locations["Blacksmith"]->canAccess($items)
				&& ($items->has("MoonPearl")
					&& ($items->canLiftDarkRocks()
						|| ($items->has('PegasusBoots')
							&& $this->world->getRegion('North East Dark World')->canEnter($locations, $items))));
		});

		$this->can_enter = function($locations, $items) {
			return ($items->has('MoonPearl')
					&& ($items->canLiftDarkRocks()
						|| ($items->has('Hammer') && $items->canLiftRocks())
						|| ($items->has('DefeatAgahnim') && $items->has('Hookshot')
								&& ($items->has('Hammer') || $items->canLiftRocks() || $items->has('Flippers')))))
				|| (($items->has('MagicMirror') || ($items->has('PegasusBoots') && $items->has('MoonPearl')))
					&& $this->world->getRegion('West Death Mountain')->canEnter($locations, $items));
		};

		return $this;
	}
}
