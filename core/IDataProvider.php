<?php
interface IDataProvider
{
	public function getId();

	public function getItemCount();

	public function getTotalItemCount();

	public function getData();

	public function getPagination();
}