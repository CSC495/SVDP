/*
 * JasperReports - Free Java Reporting Library.
 * Copyright (C) 2001 - 2011 Jaspersoft Corporation. All rights reserved.
 * http://www.jaspersoft.com
 *
 * Unless you have purchased a commercial license agreement from Jaspersoft,
 * the following license terms apply:
 *
 * This program is part of JasperReports.
 *
 * JasperReports is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JasperReports is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with JasperReports. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: Document.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class Document
{
	private Long id;

	private Address address;

	private Double total;

	public Address getAddress()
	{
		return address;
	}

	public void setAddress(Address address)
	{
		this.address = address;
	}

	public Long getId()
	{
		return id;
	}

	protected void setId(Long id)
	{
		this.id = id;
	}

	public Double getTotal()
	{
		return total;
	}

	public void setTotal(Double total)
	{
		this.total = total;
	}

	public String toString()
	{
		return id + ":" + total + " of " + address;
	}
}
