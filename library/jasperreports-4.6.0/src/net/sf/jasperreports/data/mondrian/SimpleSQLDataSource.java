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
package net.sf.jasperreports.data.mondrian;

import java.io.PrintWriter;
import java.sql.Connection;
import java.sql.SQLException;

import javax.sql.DataSource;

/**
 * @author Veaceslav Chicu (schicu@users.sourceforge.net)
 * @version $Id: SimpleSQLDataSource.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class SimpleSQLDataSource implements DataSource {
	private Connection connection;
	private PrintWriter pw = new PrintWriter(System.out);
	private int loginTimeout = 0;

	public SimpleSQLDataSource(Connection connection) {
		this.connection = connection;
	}

	public PrintWriter getLogWriter() throws SQLException {
		return pw;
	}

	public void setLogWriter(PrintWriter out) throws SQLException {
		pw = out;
	}

	public void setLoginTimeout(int seconds) throws SQLException {
		loginTimeout = seconds;
	}

	public int getLoginTimeout() throws SQLException {
		return loginTimeout;
	}

	public <T> T unwrap(Class<T> iface) throws SQLException {
		return null;
	}

	public boolean isWrapperFor(Class<?> iface) throws SQLException {
		return false;
	}

	public Connection getConnection() throws SQLException {
		return connection;
	}

	public Connection getConnection(String username, String password)
			throws SQLException {
		return connection;
	}

}
